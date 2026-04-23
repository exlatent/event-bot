<?php

declare(strict_types=1);


namespace App\Console;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Yiisoft\Queue\Queue;
use Symfony\Component\Console\Command\Command;

final class QueueCommand extends Command
{
    public function __construct(

        private Queue $queue
    ) {
        parent::__construct();
    }


    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->queue->run();
        return Command::SUCCESS;
    }
}
