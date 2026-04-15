<?php

declare(strict_types=1);

use Yiisoft\Definitions\DynamicReference;
use Yiisoft\Rbac\Db\AssignmentsStorage;
use Yiisoft\Rbac\Manager;
use Yiisoft\Rbac\ManagerInterface;
use Yiisoft\Rbac\Db\ItemsStorage;
use Yiisoft\Db\Mysql\Connection;

return [
    ManagerInterface::class => [
        'class'         => Manager::class,
        '__construct()' => [
            'itemsStorage'       => DynamicReference::to([
                'class'         => ItemsStorage::class,
                '__construct()' => [
                    'connection' => DynamicReference::to(Connection::class)
                ],
            ]),
            'assignmentsStorage' => DynamicReference::to(AssignmentsStorage::class),
        ],
    ],
];
