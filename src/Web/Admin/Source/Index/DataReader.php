<?php

declare(strict_types=1);


namespace App\Web\Admin\Source\Index;

use App\Domain\Telegram\Repository\SourceRepository;
use App\Domain\Telegram\Source;
use Yiisoft\Data\Db\QueryDataReader;
use Yiisoft\Data\Reader\Sort;
use Yiisoft\Db\Connection\ConnectionInterface;

final class DataReader extends QueryDataReader
{
    public function __construct(
        SourceRepository $repository,
        ConnectionInterface $db
    ) {
        parent::__construct(
            $db->createQuery()->from($repository->tableName())
                ->resultCallback(
                    static function (array $rows): array {
                        return array_map(
                            static fn(array $row) => new Source(
                                id: (int) $row['id'],
                                username: $row['username'],
                                title: $row['title'],
                                is_active: (bool) $row['is_active'],
                                createdAt: $row['createdAt'],
                                updatedAt: $row['updatedAt'],
                            ),
                            $rows,
                        );
                    }
                ),
            Sort::only([
                'id', 'username', 'title', 'is_active', 'createdAt', 'updatedAt'
            ]),
        );
    }

}
