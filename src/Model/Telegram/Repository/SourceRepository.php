<?php

declare(strict_types=1);

namespace App\Model\Telegram\Repository;

use App\Model\AbstractEntity;
use App\Model\AbstractRepository;
use App\Model\Telegram\Source;

final class SourceRepository extends AbstractRepository
{
    protected function tableName(): string
    {
        return 'source';
    }

    protected function entityClass(): string
    {
        return Source::class;
    }
}
