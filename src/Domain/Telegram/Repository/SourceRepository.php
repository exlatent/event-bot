<?php

declare(strict_types=1);

namespace App\Domain\Telegram\Repository;

use App\Domain\Telegram\Source;
use App\Infrastructure\AbstractRepository;
use Yiisoft\Db\Query\Query;

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

    public function getList(): array
    {
        $query = (new Query($this->connection))
            ->from(static::tableName());

        $rows = $query->all();

        return array_column($rows, 'title', 'id');
    }
}
