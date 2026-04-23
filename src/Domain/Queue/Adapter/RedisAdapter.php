<?php

declare(strict_types=1);


namespace App\Domain\Queue\Adapter;

use Predis\Client;
use Psr\Container\ContainerInterface;
use Yiisoft\Queue\Adapter\AdapterInterface;
use Yiisoft\Queue\JobStatus;
use Yiisoft\Queue\Message\Message;
use Yiisoft\Queue\Message\MessageInterface;

readonly class RedisAdapter implements AdapterInterface
{
    public function __construct(
        private Client $client,
        private string $channel,
        private ContainerInterface $container
    ) {
    }

    public function runExisting(callable $handlerCallback): void
    {
        while ($raw = $this->client->rpop($this->channel)) {
            $data = json_decode($raw, true);
            $message = new Message($data['handler'], $data['data']);

            $handler = $this->container->get($data['handler']);
            $handler($message);
        }
    }

    public function status(int|string $id): JobStatus
    {
        // TODO: Implement status() method.
    }

    public function push(MessageInterface $message): MessageInterface
    {
        $this->client->lpush(
            $this->channel,
            json_encode([
                'handler' => $message->getHandlerName(),
                'data' => $message->getData(),
            ])
        );

        return $message;
    }

    public function subscribe(callable $handlerCallback): void
    {
        // TODO: Implement subscribe() method.
    }
}
