<?php

declare(strict_types=1);

namespace App\Web\Telegram\Callbacks;

use App\Domain\Event\Event;
use App\Domain\Event\Repository\EventRepository;
use App\Shared\ApplicationDateTime;
use App\Web\Telegram\Command\StartCommand;
use App\Web\Telegram\DialogState;
use App\Web\Telegram\Period;
use App\Web\Telegram\Settings;
use App\Web\Telegram\Widget\KeyboardWidget;
use DateTimeZone;
use Predis\Client;
use Psr\Log\LoggerInterface;
use Telegram\Bot\Api;
use Yiisoft\Db\Connection\ConnectionInterface;
use Yiisoft\Json\Json;

final readonly class DigestCallback
{
    public function __construct(
        private Api $bot,
        private EventRepository $repository,
        private Client $redis,
        private LoggerInterface $logger
    ) {
    }

    public function handle(array $callback): void
    {
        $chatId = $callback['chat_id'];
        $key = 'user:' . $chatId . ':message';
        if (!$this->redis->exists($key)) {
            return;
        }
        $redis_message = json_decode($this->redis->get($key));
        $message_id = $redis_message->message_id;
        $data = Json::decode($callback['data']['data']) ?? [];
        $page = max(1, (int)($data['page'] ?? 1));
        $max = Settings::EVENTS_MAX_PER_PAGE;

        if (!isset($data['period'])) {
            return;
        }

        $events = $this->getData($data['period'], $page);
        $hasNext = count($events) ===  $max + 1;
        if (empty($events)) {
            if ($redis_message->state !== DialogState::EMPTY_RESULT) {
                $redis_message->state = DialogState::EMPTY_RESULT;
                $this->bot->editMessageText([
                    'chat_id'      => $chatId,
                    'reply_markup' => KeyboardWidget::render(),
                    'message_id'   => $message_id,
                    'text'         => 'События не найдены, попробуйте изменить условия поиска.',
                ]);
            }
        } else {
            unset($events[$max]);
            $message = $this->renderMessage($events, $data['period'], $page);
            $this->send($chatId, $message_id, $message, $data['period'], $page, $hasNext);
            $redis_message->state = 'digest';
        }
        $this->redis->del($key);
        $this->redis->set($key, Json::encode($redis_message));
    }

    private function send(int|string $chat_id, int|string $message_id, string $message, int $period, int $page, bool $hasNext): void
    {
        try {
            $this->bot->editMessageText([
                'chat_id'                  => $chat_id,
                'message_id'               => $message_id,
                'text'                     => $message,
                'reply_markup'             => KeyboardWidget::render($period, $page, $hasNext),
                'parse_mode'               => 'HTML',
                'disable_web_page_preview' => true,
            ]);
        } catch (\Throwable $e) {
            if (str_contains($e->getMessage(), 'message is not modified')) {
                return;
            }
        }
    }

    private function getData(mixed $period, $page): array
    {
        $from = '';
        $to = '';
        $now = ApplicationDateTime::nowLocal();

        switch ($period) {
            case Period::TODAY:
                $from = $now->setTime(0, 0);
                $to = $now->setTime(23, 59, 59);
                break;

            case Period::TOMORROW:
                $tomorrow = $now->modify('+1 day');
                $from = $tomorrow->setTime(0, 0);
                $to = $tomorrow->setTime(23, 59, 59);
                break;

            case Period::CURRENT_WEEK:
                $from = $now->setTime(0, 0);
                $to = $now->modify('sunday this week')->setTime(23, 59, 59);
                break;

            case Period::HOLIDAYS:
                $from = $now->modify('saturday this week')->setTime(0, 0);
                $to = $now->modify('sunday this week')->setTime(23, 59, 59);
                break;
        }

        $fromUtc = $from->setTimezone(new DateTimeZone('UTC'))->format('Y-m-d H:i:s');
        $toUtc = $to->setTimezone(new DateTimeZone('UTC'))->format('Y-m-d H:i:s');

        return $this->repository->findDigestEvents($fromUtc, $toUtc, $page, Settings::EVENTS_MAX_PER_PAGE);
    }

    /**
     * DAILY
     */
    private function renderMessage(array $data, int $period, int $page): string
    {
        $formatter = new \IntlDateFormatter(
            'ru_RU',
            timezone: ApplicationDateTime::DEFAULT_INPUT_TZ,
            pattern: 'd MMMM');

        $title = match ($period) {
            Period::TODAY => "🔥 События сегодня (" . $formatter->format(time()) . ")",
            Period::TOMORROW => "🗓 События завтра (" . $formatter->format(strtotime('+1 day')) . ")",

            Period::HOLIDAYS => "🎉 События на выходных ("
                . $formatter->format(strtotime('saturday this week')) . " – "
                . $formatter->format(strtotime('sunday this week')) . ")",

            Period::CURRENT_WEEK => "📅 События на этой неделе ("
                . $formatter->format(strtotime('monday this week')) . " – "
                . $formatter->format(strtotime('sunday this week')) . ")",
        };

        $message = "📌 <b>{$title}</b>\n\n";

        foreach ($data as $i => $event) {
            $index = $i + 1 + ($page - 1) * Settings::EVENTS_MAX_PER_PAGE;
            $message .= $this->formatEvent($event, $index, $period);
        }


        return $message;
    }



    /**
     * EVENT FORMAT
     */
    private function formatEvent(Event $event, int $index, int $period): string
    {
        $formatter = new \IntlDateFormatter(
            locale: 'ru_RU',
            timezone: ApplicationDateTime::DEFAULT_INPUT_TZ,
            pattern: 'EEE, d MMMM HH:mm');
        $datetime = ApplicationDateTime::toUserTz(ApplicationDateTime::fromDb($event->datetime));
        $datetime_formatted  = in_array($period, [Period::TODAY, Period::TOMORROW])
            ? $datetime->format('H:i')
            : $formatter->format($datetime);

        $text = sprintf(
            "%d. <b>%s</b>\n📍 <i>%s</i>\n🕒 <u>%s</u>\n💰 <i>%s</i>\n",
            $index,
            $this->e($event->title),
            $this->e($event->location),
            $datetime_formatted,
            $this->e($event->price)
        );

        if ($tg = $this->repository->getMessage($event)) {
            $url = "https://t.me/{$tg['username']}/{$tg['tg_id']}";
            $text .= '<a href="' . $this->e($url) . '">Источник</a>' . "\n\n";
        }

        return $text;
    }

    private function e(string $text): string
    {
        return htmlspecialchars($text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}
