<?php

namespace App\Domain\Telegram;

use App\Infrastructure\AbstractEntity;

/**
 * @property int $id
 * @property int $source_id
 * @property int $tg_id
 * @property int $source_tg_id
 * @property string $message
 * @property string $date
 * @property string $createdAt
 * @property string $analyzedAt
 * @property int $event_candidate
 * @property int $spam
 * @property int $off_topic
 * @property float $confidence
 * @property string $processedAt
 */
class Message extends AbstractEntity
{
    public function __construct(
        ?int $id = null,
        public ?int $source_id = null,
        public ?int $tg_id = null,
        public ?int $source_tg_id = null,
        public ?string $message = null,
        public ?string $date = null,
        public ?string $createdAt = null,
        public ?string $analyzedAt = null,
        public ?int $event_candidate = null,
        public ?int $spam = null,
        public ?int $off_topic = null,
        public ?float $confidence = null,
        public ?string $processedAt = null,
    ) {
        parent::__construct($id);
    }
}
