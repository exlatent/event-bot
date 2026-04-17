<?php

declare(strict_types=1);


namespace App\Domain\User;

use App\Infrastructure\AbstractEntity;

/**
 * @property int $id
 * @property int $tg_id
 * @property string $username
 * @property string $first_name
 * @property string $last_name
 * @property string $language_code
 * @property int $status
 * @property string $createdAt
 * @property string $updatedAt
 * @property string $lastActivity
 */
class TelegramUser extends AbstractEntity
{
    public function __construct(
        ?int $id = null,
        public ?int $tg_id = null,
        public ?string $username = null,
        public ?string $first_name = null,
        public ?string $last_name = null,
        public ?string $language_code = null,
        public ?int $status = 1,
        public ?string $createdAt = null,
        public ?string $updatedAt = null,
        public ?string $lastActivity = null
    ) {
        parent::__construct($id);
    }

    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;
}
