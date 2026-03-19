<?php

namespace App\Model\Telegram\Repository;

use App\Model\AbstractRepository;
use App\Model\Telegram\Message;
use Yiisoft\Db\Expression\Expression;
use Yiisoft\Db\Query\Query;

final class MessageRepository extends AbstractRepository
{
    protected function entityClass(): string
    {
        return Message::class;
    }

    protected function tableName(): string
    {
        return 'message';
    }

    public function getSourceScores(): array
    {
        $query = (new Query($this->connection))
            ->select([
                'score' => (new Expression(
                    'ROUND(
                          GREATEST(
                            (SUM(event_candidate) - 2*SUM(spam) - SUM(off_topic)) / COUNT(*),
                            0
                          ),
                          2)'
                )),
                'source_id'
            ])
            ->from($this->tableName())
            ->groupBy('source_id')
            ->indexBy('source_id');

        return $query->column();
    }

    public function findEventCandidates(int $limit = 20): array
    {
        $rows = (new Query($this->connection))
            ->from($this->tableName())
            ->where([
                'event_candidate' => 1,
                'spam'            => 0,
                'off_topic'       => 0,
                'processedAt'     => null
            ])
            ->andWhere(['>', 'confidence', 0.7])
            ->limit($limit)
            ->all();

        return array_map(fn($row) => $this->fromRow($row), $rows);
    }

}
