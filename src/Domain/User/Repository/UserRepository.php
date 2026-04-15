<?php

declare(strict_types=1);

namespace App\Domain\User\Repository;

use App\Domain\User\User;
use App\Infrastructure\AbstractRepository;

class UserRepository extends AbstractRepository
{

    protected function entityClass(): string
    {
       return User::class;
    }

    public static function tableName(): string
    {
        return 'user';
    }
}
