<?php

declare(strict_types=1);


namespace App\Web\Telegram;

use App\Domain\User\Repository\UserRepository;
use danog\MadelineProto\Namespace\Users;
use Psr\Log\LoggerInterface;
use Telegram\Bot\Api;
use Yiisoft\Json\Json;
use Yiisoft\Log\Logger;

final class Handler
{
    public function __construct(
        private Router $router,
        private UserHandler $userHandler,
        private Api $bot,
    ) {}

    public function handle(array $update): void
    {
        $this->userHandler->sync($update);

        if (isset($update['message'])) {
            $this->router->handleMessage($update['message']);
        }

        if (isset($update['callback_query'])) {
            if(isset($update['callback_query']['message'])) {
                $this->bot->deleteMessage([
                    'chat_id' => $update['callback_query']['message']['chat']['id'],
                    'message_id' => $update['callback_query']['message']['message_id'],
                ]);
            }
            $this->router->handleCallback($update['callback_query']);
        }
    }
}
