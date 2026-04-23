<?php

declare(strict_types=1);

use App\Console;

return [
    'admin:add' => Console\AddAdminCommand::class,
    'mtinit' => Console\MPInitCommand::class,
    'queue-worker:run' => Console\QueueCommand::class,
    ...require __DIR__.'/commands/events.php',
];
