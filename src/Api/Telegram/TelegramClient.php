<?php

declare(strict_types=1);

namespace App\Api\Telegram;

use danog\MadelineProto\API;
use danog\MadelineProto\Logger as MadelineProtoLogger;
use danog\MadelineProto\Settings;
use danog\MadelineProto\Settings\AppInfo;
use danog\MadelineProto\Settings\Logger;

final class TelegramClient
{
    private ?API $client = null;

    public function __construct(
        private int $apiId,
        private string $apiHash,
        private string $sessionPath
    ) {
        // Настройки API, без запуска start()
        $settings = new Settings();
        $appInfo = new AppInfo();
        $logger = new Logger();
        $logger->setType(2);
        $logger->setExtra('runtime/logs/madeline.log');
        $settings->setLogger($logger);
        $appInfo->setApiId($this->apiId);
        $appInfo->setApiHash($this->apiHash);
        $settings->setAppInfo($appInfo);

        $this->client = new API($this->sessionPath, $settings);
    }

    private function ensureConnected(): void
    {
        if (!$this->client->getAuthorization()) {
            // Это должен быть CLI или ручной старт один раз
            // В вебе не вызывать start(), иначе будет визард
            throw new \RuntimeException(
                'Telegram session not authorized. '
                . 'Сначала запустите telegram-init.php через CLI для авторизации.'
            );
        }
    }

    public function getMe(): array
    {
        $this->ensureConnected();
        return $this->client->getSelf();
    }

    public function getChannelMessages(string $channel, int $limit = 20): array
    {
        $this->ensureConnected();
        $history = $this->client->messages->getHistory([
            'peer' => $channel,
            'limit' => $limit
        ]);

        return $history['messages'] ?? [];
    }

    public function connect()
    {
        $this->client->start();
    }

    public function getApi(): ?API
    {
        return $this->client;
    }
}
