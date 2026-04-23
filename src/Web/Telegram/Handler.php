<?php

declare(strict_types=1);


namespace App\Web\Telegram;

use Psr\Log\LoggerInterface;
use Telegram\Bot\Api;

final readonly class Handler
{
    public function __construct(
        private Router $router,
        private UserHandler $userHandler,
        private Api $bot,
        private LoggerInterface $logger
    ) {}

    const string TYPE_MESSAGE = 'message';
    const string TYPE_CALLBACK = 'callback';

    public function handle(array $update): void
    {
        try {
            $data = $update['payload'] ?? null;
            $user = $data['user'] ?? null;

            if(!$data) {exit();}
            if($user) {
                $this->userHandler->sync($user, $data['data']['status'] ?? null);
            }
            if ($data['type'] === self::TYPE_MESSAGE) {
                $this->router->handleMessage($data);
            }

            if ($data['type'] === self::TYPE_CALLBACK) {
                if(isset($data['message_id'])) {
                    $this->bot->deleteMessage([
                        'chat_id' => $data['chat_id'],
                        'message_id' => $data['message_id'],
                    ]);
                }
                $this->router->handleCallback($data);
            }
        } catch (\Throwable $e) {
            $this->logger->error($e->getMessage());
        }
    }
}
