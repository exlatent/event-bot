<?php

declare(strict_types=1);

namespace App\Shared;

final readonly class ApplicationParams
{
    public function __construct(
        public string $name = 'Мой проект',
        public string $charset = 'UTF-8',
        public string $locale = 'ru',
        public int $telegramApiId = 0,
        public string $telegramApiHash = '',
        public string $telegramSessionPath = '',
        public string $telegramBotToken = '',
        public string $openaiApiKey = '',
    ) {}
}
