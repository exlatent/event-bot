<?php

declare(strict_types=1);

return [
    'events:get-source'      => \App\Console\Events\GetSourceCommand::class,
    'events:get-message'     => \App\Console\Events\GetMessageCommand::class,
    'events:classify'        => \App\Console\Events\MessageClassifyCommand::class,
    'events:source-evaluate' => \App\Console\Events\SourceEvaluateCommand::class,
    'events:event-generate'  => \App\Console\Events\GenerateEventsCommand::class,
    'events:deduplicate'     => \App\Console\Events\DeduplicateCommand::class,
    'events:publish'         => \App\Console\Events\PublishCommand::class,
];
