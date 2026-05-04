<?php

namespace App\Console;

use App\Api\Telegram\TelegramClient;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class TelegramLoginCommand extends Command
{
    public function __construct(
        private TelegramClient $telegram
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Starting Telegram authorization...');
        $api = $this->telegram->connect();
        $me = $api->getSelf();
        if (!$me) {

            echo "Enter phone (+995...): ";
            $phone = trim(fgets(STDIN));
            $api->phoneLogin($phone);

            echo "Enter Telegram code: ";
            $code = trim(fgets(STDIN));
            $auth = $api->completePhoneLogin($code);
            if ($auth['_'] === 'account.password') {
                echo "Please enter your password (hint: {$auth['hint']} ) ";
                $pass = trim(fgets(STDIN));
                $api->complete2falogin($pass);
            }
            $me = $api->getSelf();
            $output->writeln('Authorized as: ' . ($me['username'] ?? 'unknown'));

            return Command::SUCCESS;
        } else {
            echo "Already authorized as: " . ($me['username'] ?? 'unknown') . PHP_EOL;
            return Command::FAILURE;
        }
    }
}
