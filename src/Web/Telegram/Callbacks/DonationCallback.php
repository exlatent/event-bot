<?php

declare(strict_types=1);


namespace App\Web\Telegram\Callbacks;

use App\Web\Telegram\DialogState;
use App\Web\Telegram\Widget\KeyboardWidget;
use Predis\Client;
use Telegram\Bot\Api;
use Yiisoft\Json\Json;

final readonly class DonationCallback
{
    public function __construct(
        private Api $bot,
        private Client $redis,
    )
    {

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

        $this->bot->editMessageText([
            'chat_id' => $chatId,
            'message_id' => $message_id,
            'text' =>
                "❤️ <b>Поддержать проект</b>\n\n" .
                "Проект работает на добровольной основе.\n" .
                "Любая поддержка помогает оплачивать хостинг и токены для ИИшечки.\n\n" .
                "💳 <b>Тиньк:</b> <a href='https://www.tinkoff.ru/rm/r_fRdDgjfWAd.DaDvHWRzGQ/m69HM3515'>Отправить донат</a>\n" .
                "🏦 <b>TBC (GE):</b> " .
                "<code>GE76TB7899345068100048</code>",
            'parse_mode' => 'HTML',
            'disable_web_page_preview' => true,
            'reply_markup' => KeyboardWidget::render(),
        ]);

        if ($redis_message->state !== DialogState::DONATION) {
            $redis_message->state = DialogState::DONATION;

        }

        $this->redis->del($key);
        $this->redis->set($key, Json::encode($redis_message));
    }

}
