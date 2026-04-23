<?php

declare(strict_types=1);

namespace App\Web\Telegram\Callbacks;

use App\Domain\Event\Event;
use App\Domain\Event\Repository\EventRepository;
use App\Shared\ApplicationDateTime;
use App\Web\Telegram\Command\StartCommand;
use DateTimeZone;
use Telegram\Bot\Api;
use Yiisoft\Db\Connection\ConnectionInterface;
use Yiisoft\Json\Json;

final readonly class DigestCallback
{
    public function __construct(
        private Api $bot,
        private EventRepository $repository
    ) {}

    public function handle(array $callback): void
    {
        $data = Json::decode($callback['data']['data']) ?? [];

        if (!isset($data['period'])) {
            return;
        }

        $events = $this->getData($data['period']);

        $chatId = $callback['chat_id'];

        if (empty($events)) {
            $this->bot->sendMessage([
                'chat_id' => $chatId,
                'text'    => 'События не найдены, попробуйте изменить условия поиска.',
            ]);
            return;
        }

        if ($this->isDaily($data['period'])) {
            $message = $this->renderDailyMessage($events, $data['period']);

            $this->send($chatId, $message);
            return;
        }

        if ($this->isWeekly($data['period'])) {
            foreach ($this->renderWeeklyMessages($events, $data['period']) as $message) {
                $this->send($chatId, $message);
            }
        }
    }

    private function send(int|string $chatId, string $message): void
    {
        $this->bot->sendMessage([
            'chat_id'                  => $chatId,
            'text'                     => $message,
            'parse_mode'               => 'HTML',
            'disable_web_page_preview' => true,
        ]);
    }

    private function isDaily(int $period): bool
    {
        return in_array($period, [
            StartCommand::TODAY,
            StartCommand::TOMORROW,
        ], true);
    }

    private function isWeekly(int $period): bool
    {
        return in_array($period, [
            StartCommand::CURRENT_WEEK,
            StartCommand::NEXT_WEEK,
        ], true);
    }

    private function getData(mixed $period): array
    {
        $from = '';
        $to = '';
        $now = ApplicationDateTime::nowLocal();

        switch ($period) {
            case StartCommand::TODAY:
                $from = $now->setTime(0, 0);
                $to = $now->setTime(23, 59, 59);
                break;

            case StartCommand::TOMORROW:
                $tomorrow = $now->modify('+1 day');
                $from = $tomorrow->setTime(0, 0);
                $to = $tomorrow->setTime(23, 59, 59);
                break;

            case StartCommand::CURRENT_WEEK:
                $from = $now->setTime(0, 0);
                $to = $now->modify('sunday this week')->setTime(23, 59, 59);
                break;

            case StartCommand::NEXT_WEEK:
                $from = $now->modify('monday next week')->setTime(0, 0);
                $to = $now->modify('sunday next week')->setTime(23, 59, 59);
                break;
        }

        $fromUtc = $from->setTimezone(new DateTimeZone('UTC'))->format('Y-m-d H:i:s');
        $toUtc = $to->setTimezone(new DateTimeZone('UTC'))->format('Y-m-d H:i:s');

        return $this->repository->findDigestEvents($fromUtc, $toUtc);
    }

    /**
     * DAILY
     */
    private function renderDailyMessage(array $data, int $period): string
    {
        $formatter = new \IntlDateFormatter('ru_RU', pattern: 'd MMMM');

        $title = match ($period) {
            StartCommand::TODAY => "🔥 События сегодня (" . $formatter->format(time()) . ")",
            StartCommand::TOMORROW => "🗓 События завтра (" . $formatter->format(strtotime('+1 day')) . ")",
            default => "События",
        };

        $message = "📌 <b>{$title}</b>\n\n";

        foreach ($data as $i => $event) {
            $message .= $this->formatEvent($event, $i + 1);
        }

        return $message;
    }

    /**
     * WEEKLY → массив сообщений (по дням)
     */
    private function renderWeeklyMessages(array $data, int $period): array
    {
        $formatter = new \IntlDateFormatter('ru_RU', pattern: 'd MMMM');

        $grouped = [];

        foreach ($data as $event) {
            $key = date('Y-m-d', strtotime($event->datetime));
            $grouped[$key][] = $event;
        }

        $messages = [];

        foreach ($grouped as $date => $events) {
            $timestamp = strtotime($date);

            $message = "🔸 <b>" . mb_convert_case(
                    $formatter->format($timestamp),
                    MB_CASE_TITLE,
                    "UTF-8"
                ) . "</b>\n\n";

            foreach ($events as $i => $event) {
                $message .= $this->formatEvent($event, $i + 1);
            }

            $messages[] = $message;
        }

        return $messages;
    }

    /**
     * EVENT FORMAT
     */
    private function formatEvent(Event $event, int $index): string
    {
        $text = sprintf(
            "%d. <b>%s</b>\n📍 <i>%s</i>\n🕒 <u>%s</u>\n💰 <i>%s</i>\n",
            $index,
            $this->e($event->title),
            $this->e($event->location),
            ApplicationDateTime::toUserTz(ApplicationDateTime::fromDb($event->datetime))->format('H:i'),
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
