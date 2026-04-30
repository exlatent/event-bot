<?php

declare(strict_types=1);


namespace App\Web\Admin\Event\Index;

use App\Domain\Event\Event;
use Yiisoft\Data\Db\QueryDataReader;
use Yiisoft\Data\Reader\Sort;
use Yiisoft\Db\Connection\ConnectionInterface;
use App\Domain\Event\Repository\EventRepository;

final class DataReader extends QueryDataReader
{
    public function __construct(
        EventRepository $repository,
        ConnectionInterface $db
    ) {
        parent::__construct(
            $db->createQuery()->from($repository->tableName())
                ->resultCallback(
                    static function (array $rows): array {
                        return array_map(
                            static fn(array $row) => new Event(
                                id: (int)$row['id'],
                                message_id: (int)$row['message_id'],
                                title: $row['title'],
                                datetime: $row['datetime'],
                                location: $row['location'],
                                price: $row['price'],
                                state: (int)$row['state']
                            ),
                            $rows,
                        );
                    }
                ),
            Sort::only([
                'id', 'title', 'datetime'
            ]),

        );
    }

}
