<?php

declare(strict_types=1);

namespace App\Console\Events;

use App\Api\Telegram\TelegramClient;
use App\Domain\Telegram\Message;
use App\Domain\Telegram\Repository\MessageRepository;
use App\Domain\Telegram\Repository\SourceRepository;
use App\Shared\ApplicationDateTime;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Yiisoft\Db\Connection\ConnectionInterface;

#[AsCommand(
    name: 'events:get-message',
    description: 'Get messages from Telegram sources'
)]
final class GetMessageCommand extends Command
{
    protected static string $defaultName = 'events:get-message';

    public function __construct(
        private readonly ConnectionInterface $connection,
        private readonly TelegramClient $client
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $api = $this->client->getApi();
            $message_repo = new MessageRepository($this->connection);
            $source_repo = new SourceRepository($this->connection);

            foreach ($source_repo->findBy(['is_active' => 1]) as $source) {

                $result = $api->messages->getHistory([
                    'peer'   => $source->username,
                    'limit'  => 10,
                    'min_id' => $source->last_message_id ?? 0
                ]);

                if ($result['messages']) {
                    foreach ($result['messages'] as $message) {
                        if (!empty($message['message'])) {
                            $message_entity = new Message(
                                source_id: $source->id,
                                tg_id: $message['id'] ?? '',
                                source_tg_id: $message['peer_id'] ?? '',
                                message: $message['message'],
                                date: isset($message['date'])
                                    ? ApplicationDateTime::toDb(ApplicationDateTime::fromTimestamp($message['date']))
                                    : null,
                                createdAt: ApplicationDateTime::toDb(ApplicationDateTime::now()),
                            );

                            $message_repo->save($message_entity);
                        }
                    }
                    $source->last_message_id = $result['messages'][0]['id'] ?? null;
                    $source_repo->save($source);
                }
            }

            return Command::SUCCESS;
        } catch (\Throwable $e) {
            $output->writeln('Error: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
