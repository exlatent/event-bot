<?php

declare(strict_types=1);


namespace App\Domain\Identity;

use App\Domain\User\Repository\UserRepository;
use Yiisoft\Auth\IdentityInterface;
use Yiisoft\Auth\IdentityRepositoryInterface;
use Yiisoft\Db\Connection\ConnectionInterface;

final class IdentityRepository implements IdentityRepositoryInterface
{
    public function __construct(
        private readonly ConnectionInterface $connection,
        protected UserRepository $repository
    )
    {
        $this->repository = new UserRepository($this->connection);
    }

    public function findIdentity(string $id) : ?IdentityInterface
    {
        /** @var IdentityInterface|null */
        return $this->repository->findOne(['id' => $id]);
    }

    public function findIdentityByUsername(string $username) : ?IdentityInterface
    {
        /** @var IdentityInterface|null */
        return $this->repository->findOne(['username' => $username]);
    }



}
