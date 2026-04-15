<?php

use Psr\Container\ContainerInterface;
use App\Shared\ApplicationParams;
use Telegram\Bot\Api;

return [

    Api::class => static function (ContainerInterface $container) {

        $params = $container->get(ApplicationParams::class);

        return new Api($params->telegramBotToken);
    },
];
