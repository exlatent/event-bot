<?php

use App\Api\Telegram\TelegramClient;
use Psr\Container\ContainerInterface;
use App\Shared\ApplicationParams;

return [

    TelegramClient::class => static function (ContainerInterface $container) {

        $params = $container->get(ApplicationParams::class);

        return new TelegramClient(
            $params->telegramApiId,
            $params->telegramApiHash,
            $params->telegramSessionPath,
        );
    },
];
