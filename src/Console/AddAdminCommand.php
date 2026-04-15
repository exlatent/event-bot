<?php

declare(strict_types=1);

namespace App\Console;

use App\Domain\Rbac\AdminRole;
use App\Domain\User\Repository\UserRepository;
use App\Domain\User\User;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Yiisoft\Db\Connection\ConnectionInterface;
use Yiisoft\Rbac\Assignment;
use Yiisoft\Rbac\Db\AssignmentsStorage;
use Yiisoft\Rbac\Db\ItemsStorage;
use Yiisoft\Yii\Console\ExitCode;

final class AddAdminCommand extends Command
{
    public function __construct(
        private readonly AssignmentsStorage $assignmentStorage,
        private readonly ItemsStorage $itemsStorage,
        private readonly ConnectionInterface $connection
    ) {
        parent::__construct();
    }

    protected function configure()
    {
        parent::configure();
        $this->setName('admin:add');
        $this->setDescription('Add admin user');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $repo = new UserRepository($this->connection);
        $helper = $this->getHelper('question');

        $emailQuestion = new Question('Enter email: ');
        $email = $helper->ask($input, $output, $emailQuestion);

        if ($repo->findOne(['email' => $email])) {
            $output->writeln('User already exists');
            return ExitCode::OK;
        }

        if (!$email) {
            $output->writeln('<error>Email is required</error>');
            return Command::FAILURE;
        }

        $passwordQuestion = new Question('Enter password: ');
        $passwordQuestion->setHidden(true);
        $password = $helper->ask($input, $output, $passwordQuestion);

        if (!$password) {
            $output->writeln('<error>Password is required</error>');
            return Command::FAILURE;
        }

        $user = new User(
            null,
            $email,
            $email,
            null,
            null,
            10,
            date('Y-m-d H:i:s'),
            date('Y-m-d H:i:s'),
        );

        $user->setPassword($password);
        $user->generateAuthKey();
        $repo->save($user);

        $role = $this->itemsStorage->get(AdminRole::ROLE_NAME);
        $assignment = (new Assignment((string)$user->id, $role->getName(), time()));
        $this->assignmentStorage->add($assignment);

        return ExitCode::OK;
    }
}
