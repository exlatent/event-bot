<?php

declare(strict_types=1);


namespace App\Console\Events;

use App\Exceptions\InvalidJsonException;
use App\Model\Telegram\Repository\MessageRepository;
use App\Shared\ApplicationParams;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Yiisoft\Db\Connection\ConnectionInterface;
use Yiisoft\Json\Json;

#[AsCommand(
    name: 'events:classify-message',
    description: 'Classify messages as event candidate, spam or offtopic'
)]
final class MessageClassifyCommand extends Command
{
    protected static string $defaultName = 'events:get-source';

    public function __construct(
        private readonly ConnectionInterface $connection,
        private readonly ApplicationParams $params
    ) {
        parent::__construct();
    }


    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $repo = new MessageRepository($this->connection);
            $total = 0;
            while (true) {
                $batch = $repo->findBy(['analyzedAt' => null], 20);
                if (empty($batch)) {
                    break;
                }

                if ($count = $this->batchExecute($batch)) {
                    $total += $count;
                    $output->writeln("Processed $total messages");
                }
            }
            return Command::SUCCESS;
        } catch (\Throwable $e) {
            echo $e->getMessage();
            return Command::FAILURE;
        }
    }

    private function batchExecute(array $batch): int
    {
        $count = 0;
        $repo = new MessageRepository($this->connection);
        $client = \OpenAI::client($this->params->openaiApiKey);
        foreach ($batch as $message) {
            $message_json = Json::encode([
                'id'   => $message->id,
                'text' => $message->message,
            ]);
            $response = $client->chat()->create([
                'model'    => 'gpt-5-nano',
                'messages' => [
                    [
                        'role'    => 'system',
                        'content' => 'Ты анализируешь сообщения из Telegram-каналов города Батуми.'
                    ],
                    [
                        'role'    => 'user',
                        'content' => '
                Проанализируй список сообщений из Telegram.
                Для каждого сообщения определи:
                event_candidate — может ли сообщение содержать событие
                spam — является ли сообщение спамом
                offtopic — не относится ли сообщение к афише города
                confidence — уверенность от 0 до 1

                Ответ верни в JSON массиве.

                Формат ответа:
                  {
                    "id": 1,
                    "event_candidate": 1,
                    "spam": 0,
                    "offtopic": 0,
                    "confidence": 0.87
                  }
                Сообщения:
                    '.$message_json
                    ]
                ]
            ]);

            $text = $response['choices'][0]['message']['content'];
            try {
                $result = Json::decode($text)[0];
                if (
                    !isset($result['event_candidate'])
                    || !isset($result['spam'])
                    || !isset($result['offtopic'])
                    || !isset($result['confidence'])
                ) {
                    continue;
                }
                $message->analyzedAt = date('Y-m-d H:i:s');
                $message->event_candidate = $result['event_candidate'];
                $message->spam = $result['spam'];
                $message->off_topic = $result['offtopic'];
                $message->confidence = $result['confidence'];
                $repo->save($message);
                ++$count;

            } catch (\JsonException $e) {
                throw new InvalidJsonException('Invalid JSON response '.$e->getMessage());
            }
        }
        return $count;
    }

}
