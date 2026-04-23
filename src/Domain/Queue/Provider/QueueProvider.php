<?php

declare(strict_types=1);


namespace App\Domain\Queue\Provider;

use BackedEnum;
use Yiisoft\Queue\Provider\QueueProviderInterface;
use Yiisoft\Queue\Queue;
use Yiisoft\Queue\QueueInterface;

final readonly class QueueProvider implements QueueProviderInterface
{
    public function __construct(
        private Queue $queue
    ) {}

    public function get(BackedEnum|string $name): QueueInterface
    {
        return $this->queue;
    }

    public function has(BackedEnum|string $name): bool
    {
        return in_array($name, $this->getNames(), true);
    }

    public function getNames(): array
    {
       return [
           'event_bot'
       ];
    }
}
