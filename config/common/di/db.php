<?php

use Yiisoft\Db\Connection\ConnectionInterface;
use Yiisoft\Db\Mysql\Connection;
use Yiisoft\Db\Mysql\Driver;

/** @var array $params */

return [
    ConnectionInterface::class => [
        'class' => Connection::class,
        '__construct()' => [
            'driver' => new Driver(
                $params['yiisoft/db']['dsn'],
                $params['yiisoft/db']['username'],
                $params['yiisoft/db']['password'],
            ),
        ],
    ],
];
