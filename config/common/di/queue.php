<?php

use App\Domain\Queue\Adapter\RedisAdapter;
use App\Domain\Queue\Provider\QueueProvider;
use Predis\Client;
use Yiisoft\Definitions\Reference;
use Yiisoft\Queue\Queue;

return [
    RedisAdapter::class => [
        '__construct()' => [
            'client' => Reference::to(Client::class),
            'channel' => 'event-bot',
        ],
    ],

    Queue::class => [
        'class' => Queue::class,
        '__construct()' => [
            'adapter' => Reference::to(RedisAdapter::class)
        ],
    ],

    \Yiisoft\Queue\Provider\QueueProviderInterface::class => Reference::to(QueueProvider::class),
];
