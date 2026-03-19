<?php

declare(strict_types=1);

use App\Console;

return [
    'hello' => Console\HelloCommand::class,
    'mtinit' => Console\MPInitCommand::class,
    ...require __DIR__.'/commands/events.php',
];
