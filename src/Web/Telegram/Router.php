<?php

declare(strict_types=1);


namespace App\Web\Telegram;

use App\Web\Telegram\Callbacks\DigestCallback;
use App\Web\Telegram\Command\StartCommand;
use Psr\Log\LoggerInterface;
use Telegram\Bot\Api;
use Yiisoft\Json\Json;

final readonly class Router
{
    public function __construct(
        private StartCommand $startCommand,
        private DigestCallback $digestCallback,
        private Api $bot
    ) {
    }

    public function handleMessage(array $data): void
    {
        $text = $data['data']['text'] ?? '';

        if ($text === '/start') {
            $this->startCommand->handle($data);
        }

//        if ($text === '/help') {
//            $this->helpCommand->handle($message);
//            return;
//        }

        // fallback
    }

    public function handleCallback(array $callback): void
    {
        $data = Json::decode($callback['data']['data']) ?? [];
        $action = $data['action'] ?? '';

        if ($action === 'digest') {
            $this->digestCallback->handle($callback);
        }
    }
}
