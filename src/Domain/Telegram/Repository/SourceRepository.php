<?php

declare(strict_types=1);

namespace App\Domain\Telegram\Repository;

use App\Domain\Telegram\Source;
use App\Infrastructure\AbstractRepository;

final class SourceRepository extends AbstractRepository
{
    public static function tableName(): string
    {
        return 'source';
    }

    protected function entityClass(): string
    {
        return Source::class;
    }
}
