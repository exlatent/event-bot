<?php

declare(strict_types=1);


namespace App\Console;

use App\Console\Events\DeduplicateCommand;
use App\Console\Events\GenerateEventsCommand;
use App\Console\Events\GetMessageCommand;
use App\Console\Events\GetSourceCommand;
use App\Console\Events\MessageClassifyCommand;
use App\Console\Events\PublishCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'events:update',
    description: 'Runs events update pipeline'
)]
final class EventsUpdateCommand extends Command
{
    public function __construct(
        private readonly GetSourceCommand $getSourceCommand,
        private readonly GetMessageCommand $getMessageCommand,
        private readonly MessageClassifyCommand $classifyCommand,
        private readonly GenerateEventsCommand $generateCommand,
        private readonly DeduplicateCommand $deduplicateCommand,
        private readonly PublishCommand $publishCommand,
    ) {
        parent::__construct();
    }

    protected static string $defaultName = 'events:update';

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln("\rStart events:get-source");
        $this->getSourceCommand->execute($input, $output);
        $output->writeln("\rFinish events:get-source");

        $output->writeln("\rStart events:get-message");
        $this->getMessageCommand->execute($input, $output);
        $output->writeln("\rFinish events:get-message");

        $output->writeln("\rStart events:classify");
        $this->classifyCommand->execute($input, $output);
        $output->writeln("\rFinish events:classify");

        $output->writeln("\rStart events:event-generate");
        $this->generateCommand->execute($input, $output);
        $output->writeln("\rFinish events:event-generate");

        $output->writeln("\rStart events:deduplicate");
        $this->deduplicateCommand->execute($input, $output);
        $output->writeln("\rFinish events:deduplicate");

        $output->writeln("\rStart events:publish");
        $this->publishCommand->execute($input, $output);
        $output->writeln("\rFinish events:publish");

        return Command::SUCCESS;
    }
}
