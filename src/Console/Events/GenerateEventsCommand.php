<?php

declare(strict_types=1);

namespace App\Console\Events;

use App\Domain\Event\Event;
use App\Domain\Event\Repository\EventRepository;
use App\Domain\Telegram\Repository\MessageRepository;
use App\Infrastructure\Exceptions\InvalidJsonException;
use App\Shared\ApplicationDateTime;
use App\Shared\ApplicationParams;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Yiisoft\Db\Connection\ConnectionInterface;
use Yiisoft\Json\Json;

#[AsCommand(
    name: 'events:get-message',
    description: 'Generate events from Telegram messages'
)]
final class GenerateEventsCommand extends Command
{
    protected static string $defaultName = 'events:get-message';

    public function __construct(
        private readonly ConnectionInterface $connection,
        private readonly ApplicationParams $params,

    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $repo = new MessageRepository($this->connection);
            $messages_processed = 0;
            $events_created = 0;
            while (true) {
                $batch = $repo->findEventCandidates();
                if (empty($batch)) {
                    break;
                }

                $result = $this->executeBatch($batch);

                if (!empty($result)) {
                    $messages_processed += $result['message'];
                    $events_created += $result['event'];
                    $output->writeln("Processed $messages_processed messages, created $events_created events");
                }
            }
            return Command::SUCCESS;
        } catch (\Throwable $e) {
            echo $e->getMessage();
            return Command::FAILURE;
        }
    }

    private function executeBatch(array $batch): array
    {
        $count_messages = 0;
        $count_events = 0;
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
                Из каждого сообщения выдели одно или несколько событий, для каждого определи:
                title — название события
                datetime — дата и время начала события
                location — место проведения
                price — стоимость посещения (если есть)

                Ответ верни в JSON массиве.

                Формат ответа:
                  [
                      {
                        "id": 1,
                        "title": "Вечер ирландской музыки",
                        "datetime": "2026-03-17 20:00:00",
                        "location": "Geo.Graphia",
                        "price": "20 GEL"
                      }
                  ]
                Сообщения:
                    '.$message_json
                    ]
                ]
            ]);


            $text = $response['choices'][0]['message']['content'];
            try {
                $result = Json::decode($text);
                foreach ($result as $event) {
                    if (
                        empty($event['title'])
                        || empty($event['datetime'])
                        || ApplicationDateTime::fromDb($event['datetime']) < ApplicationDateTime::now()
                        || empty($event['location'])
                        || empty($event['price'])
                    ) {
                        continue;
                    }
                    $event_repo = new EventRepository($this->connection);
                    $event_entity = new Event(
                        message_id: $message->id,
                        title: $event['title'],
                        datetime: ApplicationDateTime::toDb(ApplicationDateTime::fromInput($event['datetime'])),
                        location: $event['location'],
                        price: $event['price'],
                        state: Event::STATE_DRAFT,
                        createdAt: ApplicationDateTime::toDb(ApplicationDateTime::now()),
                        updatedAt: ApplicationDateTime::toDb(ApplicationDateTime::now())
                    );

                    $event_repo->save($event_entity);
                    ++$count_events;
                }

                $message->processedAt = ApplicationDateTime::toDb(ApplicationDateTime::now());
                $repo->save($message);
                ++$count_messages;

            } catch (\JsonException $e) {
                throw new InvalidJsonException('Invalid JSON response '.$e->getMessage());
            }
        }
        return ['message' => $count_messages, 'event' => $count_events];
    }
}
