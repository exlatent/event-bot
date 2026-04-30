<?php

declare(strict_types=1);

use App\Console;

return [
    'admin:add'      => Console\AddAdminCommand::class,
    'telegram:login' => Console\TelegramLoginCommand::class,
    'events:update'  => Console\EventsUpdateCommand::class,
    ...require __DIR__ . '/commands/events.php',
];
