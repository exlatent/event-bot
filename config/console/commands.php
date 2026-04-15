<?php

declare(strict_types=1);

use App\Console;

return [
    'admin:add' => Console\AddAdminCommand::class,
    'mtinit' => Console\MPInitCommand::class,
    ...require __DIR__.'/commands/events.php',
];
