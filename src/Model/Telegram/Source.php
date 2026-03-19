<?php

declare(strict_types=1);

namespace App\Model\Telegram;

use App\Model\AbstractEntity;

/**
 * @property int $id
 * @property int $tg_id
 * @property string $username
 * @property string $title
 * @property int $last_message_id
 * @property bool $is_active
 * @property float $score
 * @property int $createdAt
 * @property int $updatedAt
 */

final class Source extends AbstractEntity
{
    public function __construct(
        ?int $id = null,
        public ?int $tg_id = null,
        public string $username = '',
        public string $title = '',
        public ?int $last_message_id = null,
        public bool $is_active = true,
        public float $score = 0.00,
        public ?string $createdAt = null,
        public ?string $updatedAt = null,
    ) {
        parent::__construct($id);
    }
}
