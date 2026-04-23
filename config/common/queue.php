<?php

return [
    'handlers' => [
        \App\Domain\Queue\Handler\TelegramCallbackHandler::class,
    ],
];
