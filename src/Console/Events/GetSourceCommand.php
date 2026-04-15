<?php

declare(strict_types=1);

namespace App\Console\Events;

use App\Api\Telegram\TelegramClient;
use App\Domain\Telegram\Repository\SourceRepository;
use App\Domain\Telegram\Source;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Yiisoft\Db\Connection\ConnectionInterface;

#[AsCommand(
    name: 'events:get-source',
    description: 'Get message sources from Telegram'
)]
final class GetSourceCommand extends Command
{
    protected static string $defaultName = 'events:get-source';

    public function __construct(
        private readonly TelegramClient $client,
        private readonly ConnectionInterface $connection
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $api = $this->client->getApi();

        $repo = new SourceRepository($this->connection);

        $queries = [
            'афиша батуми',
            'батуми концерты',
            'квиз батуми',
            'анонсы Батуми',
            'мероприятия Батуми',
            'концерты Батуми',
            'театр Батуми',
            'выставки Батуми',
            'сходки Батуми',
            'вечеринки Батуми',
            'куда сходить Батуми',
            'Батуми c детьми',
        ];

        foreach ($queries as $q) {

            $result = $api->contacts->search(['q' => $q]);

            if (!empty($result['chats'])) {
                foreach ($result['chats'] as $user) {
                    $last_message_result = $api->messages->getHistory([
                        'peer'  => $user['username'],
                        'limit' => 1
                    ]);
                    if($repo->findOne(['tg_id' => $user['id']])) continue;
                    if ($last_message_result['messages'][0] && $last_message_result['messages'][0]['date'] > time() - 60 * 60 * 24 * 30) {
                        $source = new Source(
                            null,
                            $user['id'],
                            $user['username'],
                            $user['title'],
                            null,
                            true,
                            0.00,
                            date('Y-m-d H:i:s'),
                            date('Y-m-d H:i:s')
                        );

                        $repo->save($source);
                    }
                }
            }
        }

        return Command::SUCCESS;
    }
}
