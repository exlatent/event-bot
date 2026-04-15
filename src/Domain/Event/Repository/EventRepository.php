<?php

declare(strict_types=1);


namespace App\Domain\Event\Repository;

use App\Domain\Event\Event;
use App\Domain\Telegram\Message;
use App\Domain\Telegram\Repository\MessageRepository;
use App\Domain\Telegram\Repository\SourceRepository;
use App\Infrastructure\AbstractEntity;
use App\Infrastructure\AbstractRepository;
use Yiisoft\Db\Query\Query;

class EventRepository extends AbstractRepository
{
    protected function entityClass(): string
    {
        return Event::class;
    }

    public static function tableName(): string
    {
        return 'event';
    }

    public function getDedupCandidates(Event $event): array
    {
        $query = (new Query($this->connection))
            ->select(['id', 'title', 'location'])
            ->from(self::tableName())
            ->where(['datetime' => $event->datetime])
            ->andWhere(['!=', 'id', $event->id]);

        if ($event->lastCheckedAt) {
            $query->andWhere(['>', 'createdAt', $event->lastCheckedAt]);
        }

        return $query->all();
    }

    public function findNextNotDeduplicated(int $minutes_interval): AbstractEntity|Event|null
    {
        $row = (new Query($this->connection))
            ->from(self::tableName())
            ->where(['>', 'datetime', date('Y-m-d H:i:s')])
            ->andWhere(['duplicate_of_id' => null])
            ->andWhere(['<', 'lastCheckedAt', date('Y-m-d H:i:s', time() - $minutes_interval * 60)])
            ->orWhere(['lastCheckedAt' => null])
            ->orderBy('id')
            ->limit(1)
            ->one();

        return $row ? $this->fromRow($row) : null;
    }

    public function findDigestEvents(string $from, string $to)
    {
        $query =  new Query($this->connection)
            ->from(self::tableName())
            ->where(['between', 'datetime', $from, $to])
            ->andWhere(['state' => Event::STATE_PUBLISHED])
            ->orderBy('datetime');

        $rows = $query->all();

        return array_map(fn($row) => $this->fromRow($row), $rows);
    }

    /**
     * @param  Event  $event
     * @return array|null
     */
    public function getMessage(Event $event): array|null
    {
        if(!$event->message_id) {
            return null;
        }

        return new Query($this->connection)
            ->from(['m' => MessageRepository::tableName()])
            ->select(['m.tg_id', 's.username'])
            ->leftJoin(['s' => SourceRepository::tableName()], 's.id = m.source_id')
            ->where(['m.id' => $event->message_id])
            ->one()
        ;
    }
}
