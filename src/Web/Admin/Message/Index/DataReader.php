<?php

declare(strict_types=1);


namespace App\Web\Admin\Message\Index;

use App\Domain\Telegram\Message;
use App\Domain\Telegram\Repository\MessageRepository;
use Yiisoft\Data\Db\QueryDataReader;
use Yiisoft\Data\Reader\Sort;
use Yiisoft\Db\Connection\ConnectionInterface;

final class DataReader extends QueryDataReader
{
    public function __construct(
        MessageRepository $repository,
        ConnectionInterface $db
    ) {
        parent::__construct(
            $db->createQuery()->from($repository->tableName())
                ->resultCallback(
                    static function (array $rows): array {
                        return array_map(
                            static fn(array $row) => new Message(
                                id: (int) $row['id'],
                                source_id: (int) $row['source_id'],
                                message: $row['message'],
                                date: $row['date'],
                                createdAt: $row['createdAt'],
                                event_candidate: (int)$row['event_candidate'],
                                processedAt: $row['processedAt']
                            ),
                            $rows,
                        );
                    }
                ),
            Sort::only([
                'id', 'source_id', 'date', 'createdAt', 'event_candidate', 'processedAt'
            ]),
        );
    }
}

