<?php

namespace App\Web\Test;

use App\Api\Telegram\TelegramClient;
use App\Exceptions\InvalidJsonException;
use App\Model\Telegram\Message;
use App\Model\Telegram\Repository\MessageRepository;
use App\Model\Telegram\Repository\SourceRepository;
use App\Model\Telegram\Source;
use OpenAI\Client;
use Yiisoft\Db\Connection\ConnectionInterface;
use Yiisoft\Definitions\ValueDefinition;
use Yiisoft\Json\Json;
use Yiisoft\Yii\View\Renderer\ViewRenderer;

final readonly class Action
{
    public function __construct(
        private ViewRenderer $viewRenderer,
//        private TelegramClient $client,
        private ConnectionInterface $connection
    ) {
    }

    public function __invoke()
    {
        $result = (new MessageRepository($this->connection))->getSourceScores();

        var_dump($result); exit();
        return $this->viewRenderer->render(__DIR__.'/template');
    }
}
