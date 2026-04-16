<?php

declare(strict_types=1);


namespace App\Console\Events;

use App\Domain\Event\Event;
use App\Domain\Event\Repository\EventRepository;
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
    name: 'events:deduplicate',
    description: 'Deduplicate events in the database'
)]

final class DeduplicateCommand extends Command
{
    protected static string $defaultName = 'events:deduplicate';

    public function __construct(
        private readonly ConnectionInterface $connection,
        private readonly ApplicationParams $params,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $repo = new EventRepository($this->connection);
        $count_events = 0;
        $count_duplicates = 0;

        while($event = $repo->findNextNotDeduplicated(60))
        {
            $count_events++;
            $dedup_candidates = $repo->getDedupCandidates($event);
            $event->lastCheckedAt = ApplicationDateTime::toDb(ApplicationDateTime::now());
            if (!empty($dedup_candidates)) {
                $duplicates = $this->checkDuplicates($event, $dedup_candidates);
                if (!empty($duplicates)) {
                    foreach ($duplicates as $duplicate_event_id) {
                        if($duplicate_event = $repo->findOne(['id' => $duplicate_event_id])) {
                            $duplicate_event->duplicate_of_id = $event->id;
                            $repo->save($duplicate_event);
                            $count_duplicates++;
                        }
                    }
                }
            }
            $repo->save($event);
            $output->write("\rChecked events: $count_events, duplicates: $count_duplicates");
        }
        return Command::SUCCESS;
    }

    private function checkDuplicates(Event $event, array $events_data): array
    {
        $client = \OpenAI::client($this->params->openaiApiKey);
        $response = $client->chat()->create([
            'model'    => 'gpt-5-nano',
            'messages' => [
                [
                    'role'    => 'system',
                    'content' => 'Ты анализируешь массив событий на дубликаты.
                    События считаются дубликатами, если это одно и то же мероприятие,
                    даже если текст написан по-разному'
                ],
                [
                    'role'    => 'user',
                    'content' => '
                    Исходное событие: '.$event->title.', локация: '.$event->location.'
                    Кандидаты для проверки: '.json_encode($events_data).'
                Сравни событие с кандидатами и верни id дубликатов.
                Ответ верни в JSON массиве.
                Формат ответа:
                {
                  "duplicates": [id, id]
                }',
                ]
            ]
        ]);

        $text = $response['choices'][0]['message']['content'];
        try {
            return Json::decode($text)['duplicates'] ?? [];

        } catch (\JsonException $e) {
            throw new InvalidJsonException('Invalid JSON response '.$e->getMessage());
        }
    }
}
