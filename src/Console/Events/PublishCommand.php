<?php

declare(strict_types=1);


namespace App\Console\Events;

use App\Domain\Event\Event;
use App\Domain\Event\Repository\EventRepository;
use App\Shared\ApplicationParams;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Yiisoft\Db\Connection\ConnectionInterface;

#[AsCommand(
    name: 'events:publish',
    description: 'Publish original events '
)]

final class PublishCommand extends Command
{
    protected static string $defaultName = 'events:publish';

    public function __construct(
        private readonly ConnectionInterface $connection
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $repo = new EventRepository($this->connection);
        $events_to_publish = $repo->findBy([
            'state' => Event::STATE_DRAFT,
            'duplicate_of_id' => null,
        ]);

        foreach ($events_to_publish as $event) {
            $event->state = Event::STATE_PUBLISHED;
            $repo->save($event);
        }

        return Command::SUCCESS;
    }
}
