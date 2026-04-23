<?php

use App\Shared\ApplicationParams;
use Psr\Container\ContainerInterface;

return [

    \Predis\Client::class => static function (ContainerInterface $container) {

        $params = $container->get(ApplicationParams::class);

        return new \Predis\Client(
            parameters: $params->redisParams
        );
    },
];
