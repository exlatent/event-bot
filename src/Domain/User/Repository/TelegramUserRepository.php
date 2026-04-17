<?php

declare(strict_types=1);


namespace App\Domain\User\Repository;

use App\Domain\User\TelegramUser;
use App\Infrastructure\AbstractRepository;

class TelegramUserRepository extends AbstractRepository
{

    protected function entityClass(): string
    {
       return TelegramUser::class;
    }

    public static function tableName(): string
    {
        return 'telegram_user';
    }
}
