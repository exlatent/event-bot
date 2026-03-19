<?php

declare(strict_types=1);


namespace App\Model\Event\Repository;

use App\Model\AbstractRepository;
use App\Model\Event\Event;
use Yiisoft\Db\Query\Query;

class EventRepository extends AbstractRepository
{
    protected function entityClass(): string
    {
        return Event::class;
    }

    protected function tableName(): string
    {
        return 'event';
    }

    public function getDedupCandidates(Event $event): array
    {
        $query = (new Query($this->connection))
            ->select(['id', 'title', 'location'])
            ->from($this->tableName())
            ->where(['datetime' => $event->datetime])
            ->andWhere(['!=', 'id', $event->id]);

        if ($event->lastCheckedAt) {
            $query->andWhere(['>', 'createdAt', $event->lastCheckedAt]);
        }

        return $query->all();
    }

    public function findNextNotDeduplicated(int $minutes_interval): \App\Model\AbstractEntity|Event|null
    {
        $row = (new Query($this->connection))
            ->from($this->tableName())
            ->where(['>', 'datetime', date('Y-m-d H:i:s')])
            ->andWhere(['duplicate_of_id' => null])
            ->andWhere(['<', 'lastCheckedAt', date('Y-m-d H:i:s', time() - $minutes_interval * 60)])
            ->orWhere(['lastCheckedAt' => null])
            ->orderBy('id')
            ->limit(1)
            ->one();

        return $row ? $this->fromRow($row) : null;
    }
}
