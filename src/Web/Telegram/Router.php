<?php

declare(strict_types=1);


namespace App\Web\Telegram;

use App\Web\Telegram\Callbacks\DigestCallback;
use App\Web\Telegram\Command\StartCommand;
use Psr\Log\LoggerInterface;
use Yiisoft\Json\Json;

final class Router
{
    public function __construct(
        private StartCommand $startCommand,
        private DigestCallback $digestCallback,
        private LoggerInterface $logger
    ) {
    }

    public function handleMessage(array $message): void
    {
        $text = $message['text'] ?? '';

        if ($text === '/start') {
            $this->startCommand->handle($message);
        }

//        if ($text === '/help') {
//            $this->helpCommand->handle($message);
//            return;
//        }

        // fallback
    }

    public function handleCallback(array $callback): void
    {
        $data = Json::decode($callback['data']) ?? [];
        $action = $data['action'] ?? '';

        if ($action === 'digest') {
            $this->digestCallback->handle($callback);
        }
    }

}
