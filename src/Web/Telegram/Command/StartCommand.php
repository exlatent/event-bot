<?php

declare(strict_types=1);


namespace App\Web\Telegram\Command;

use App\Web\Telegram\DialogState;
use App\Web\Telegram\Widget\KeyboardWidget;
use Predis\Client;
use Psr\Log\LoggerInterface;
use Telegram\Bot\Api;
use Yiisoft\Json\Json;

final class StartCommand
{

    public function __construct(
        private readonly Api $bot,
        private readonly Client $redis,
        private LoggerInterface $logger
    ) {
    }

    public function handle(array $message): void
    {
        $chatId = $message['chat_id'];

        $this->bot->deleteMessage([
            'message_id' => $message['message_id'],
            'chat_id'    => $chatId,
        ]);

        $key = 'user:' . $chatId . ':message';
        $menu_message = [
            'chat_id'      => $chatId,
            'text'         => 'Привет! 👋 Нажми на кнопку и получи список событий',
            'reply_markup' => KeyboardWidget::render()
        ];
        if ($this->redis->exists($key)) {
            $value = json_decode($this->redis->get($key));
            $menu_message['message_id'] = $value->message_id;
            try {
                $this->bot->editMessageText($menu_message);
            } catch (\Throwable $e) {
                $this->logger->error($e->getMessage());
                $new_message = $this->bot->sendMessage($menu_message);
                $this->redis->set($key, Json::encode([
                    'message_id' => $new_message['message_id'],
                    'chat_id'    => $chatId,
                    'state'      => DialogState::START_MENU
                ]));
            }

        } else {
            $new_message = $this->bot->sendMessage($menu_message);

            $this->redis->set($key, Json::encode([
                'message_id' => $new_message['message_id'],
                'chat_id'    => $chatId,
                'state'      => DialogState::START_MENU
            ]));
        }
    }
}
