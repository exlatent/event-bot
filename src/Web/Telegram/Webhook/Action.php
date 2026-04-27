<?php

declare(strict_types=1);


namespace App\Web\Telegram\Webhook;

use App\Domain\Queue\Handler\TelegramCallbackHandler;
use HttpSoft\Message\ResponseFactory;
use Predis\Client;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Yiisoft\Queue\Message\Message;
use Yiisoft\Queue\Queue;

final readonly class Action
{
    public function __construct(
        private ResponseFactory $responseFactory,
        private Client $redis,
        private Queue $queue,
        private LoggerInterface $logger
    ) {
    }

    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $body = (string) $request->getBody();
        $update = json_decode($body, true);

        if (!is_array($update)) {
            $this->logger->warning('Invalid telegram payload', ['body' => $body]);
            return $this->responseFactory->createResponse(400);
        }

        $update_id = $update['update_id'] ?? null;

        if (!is_null($update_id) && $this->isDuplicate($update_id)) {
            return $this->setOk();
        }

        $message = new Message(
            handlerName: TelegramCallbackHandler::class,
            data: [
                'payload' => $this->normalize($update),
            ]
        );

        try {
            $this->queue->push($message);
            return $this->setOk();

        } catch (\Throwable $e) {
            $this->logger->error($e->getMessage());
            $response = $this->responseFactory->createResponse();
            $response->getBody()->write('Bot api error');
            return $response;
        }
    }

    /**
     * @return ResponseInterface
     */
    private function setOk(): ResponseInterface
    {
        $response = $this->responseFactory->createResponse();
        $response->getBody()->write('OK');
        return $response;
    }

    private function normalize(array $update): array
    {
        if (isset($update['message'])) {
            $msg = $update['message'];

            return [
                'update_id'  => $update['update_id'],
                'message_id' => $msg['message_id'] ?? null,
                'type'       => 'message',
                'chat_id'    => $msg['chat']['id'] ?? null,
                'user'       => $msg['from'] ?? null,
                'data'       => [
                    'text' => $msg['text'] ?? null,
                ],
            ];
        }

        if (isset($update['callback_query'])) {
            $cb = $update['callback_query'];

            return [
                'update_id'  => $update['update_id'],
                'type'       => 'callback',
                'id'         => $cb['id'] ?? null,
                'chat_id'    => $cb['message']['chat']['id'] ?? null,
                'message_id' => $cb['message']['message_id'] ?? null,
                'user'       => $cb['from'] ?? null,
                'data'       => [
                    'data' => $cb['data'] ?? null,
                ],
            ];
        }

        if (isset($update['my_chat_member'])) {
            $this->logger->info('my_chat_member', ['update' => $update]);
            $mcm = $update['my_chat_member'];

            return [
                'update_id' => $update['update_id'],
                'type'      => 'chat_member',
                'chat_id'   => $mcm['chat']['id'] ?? null,
                'user'      => $mcm['from'] ?? null,
                'data'      => [
                    'status' => $mcm['new_chat_member']['status'] ?? null,
                ],
            ];
        }

        return [
            'update_id' => $update['update_id'],
            'type'      => 'unknown',
            'data'      => [],
        ];
    }

    /**
     * @param $update_id
     * @return bool
     */
    private function isDuplicate($update_id): bool
    {
        $key = "tg:update:$update_id";
        $result = $this->redis->set(
            $key,
            1,
            'EX',
            3600,
            'NX'
        );

        return (string) $result !== 'OK';
    }
}
