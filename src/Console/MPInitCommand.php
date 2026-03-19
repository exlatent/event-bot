<?php

namespace App\Console;

use App\Api\Telegram\TelegramClient;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class MPInitCommand extends Command
{
    public function __construct(
        private TelegramClient $telegram
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->telegram->connect();
//        $output->writeln('Starting Telegram authorization...');
//
//        $this->telegram->connect();
//
//        $me = $this->telegram->getMe();
//
//        $output->writeln('Authorized as: ' . ($me['username'] ?? 'unknown'));

        return Command::SUCCESS;
    }
}
