<?php

use App\Domain\Event\Repository\EventRepository;
use App\Domain\User\Repository\TelegramUserRepository;
use Psr\Container\ContainerInterface;
use Yiisoft\Db\Connection\ConnectionInterface;

return [
    TelegramUserRepository::class => static function (ContainerInterface $container) {
        return new TelegramUserRepository($container->get(ConnectionInterface::class));
    }
    ,
    EventRepository::class        => static function (ContainerInterface $container) {
        return new EventRepository($container->get(ConnectionInterface::class));
    },
];
