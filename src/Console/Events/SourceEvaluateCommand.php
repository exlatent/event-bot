<?php

declare(strict_types=1);


namespace App\Console\Events;

use App\Domain\Telegram\Repository\MessageRepository;
use App\Domain\Telegram\Repository\SourceRepository;
use App\Domain\Telegram\Source;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Yiisoft\Db\Connection\ConnectionInterface;

#[AsCommand(
    name: 'events:source-evaluate',
    description: 'Evaluate message sources and unset active flag if score is too low'
)]
final class SourceEvaluateCommand extends Command
{
    protected static string $defaultName = 'events:source-evaluate';

    public function __construct(
        private readonly ConnectionInterface $connection
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $source_repo = new SourceRepository($this->connection);
        $message_repo = new MessageRepository($this->connection);
        foreach ($message_repo->getSourceScores() as $source_id => $score) {
            if($source = $source_repo->findOne(['id' => $source_id]) ) {
                /** @var Source $source */
                $source->score = (double)$score;
                if($score < 0.2) $source->is_active = false;
                $source_repo->save($source);
            }
        }
        return Command::SUCCESS;
    }

}
