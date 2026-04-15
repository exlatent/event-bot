<?php

declare(strict_types=1);

use App\Shared\ApplicationParams;

/** @var array $params */

return [
    ApplicationParams::class => [
        '__construct()' => [
            'name' => $params['application']['name'],
            'charset' => $params['application']['charset'],
            'locale' => $params['application']['locale'],

            'telegramApiId' => $params['telegram']['telegramApiId'],
            'telegramApiHash' => $params['telegram']['telegramApiHash'],
            'telegramSessionPath' => $params['telegram']['telegramSessionPath'],
            'telegramBotToken' => $params['telegram_bot']['token'],

            'openaiApiKey' => $params['open_ai']['apiKey'],
        ],
    ],
];
