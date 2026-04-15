<?php

declare(strict_types=1);

namespace App\Domain\Event;

use App\Infrastructure\AbstractEntity;

/**
 * @property int $id
 * @property int $message_id
 * @property string $title
 * @property string $datetime
 * @property string $location
 * @property string $price
 * @property int $state
 * @property int $duplicate_of_id
 * @property string $lastCheckedAt
 * @property string $createdAt
 * @property string $updatedAt
 */
final class Event extends AbstractEntity
{
    public function __construct(
        ?int $id = null,
        public ?int $message_id = null,
        public ?string $title = null,
        public ?string $datetime = null,
        public ?string $location = null,
        public ?string $price = null,
        public ?int $state = 0,
        public ?int $duplicate_of_id = null,
        public ?string $lastCheckedAt = null,
        public ?string $createdAt = null,
        public ?string $updatedAt = null
    ) {
        parent::__construct($id);
    }

    const STATE_DRAFT = 0;
    const STATE_PUBLISHED = 1;
    const STATE_CANCELLED = 2;

    static array $states = [
        self::STATE_DRAFT => 'Draft',
        self::STATE_PUBLISHED => 'Published',
        self::STATE_CANCELLED => 'Cancelled',
    ];
}
