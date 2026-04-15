<?php

declare(strict_types=1);

namespace App\Domain\User;

use App\Infrastructure\AbstractEntity;
use Yiisoft\Auth\IdentityInterface;

/**
 * @property int $id
 * @property string $username
 * @property string $email
 * @property string $password_hash
 * @property string $auth_key
 * @property int $status
 * @property string $createdAt
 * @property string $updatedAt
 */
final class User extends AbstractEntity implements IdentityInterface
{
    const MAX_LOGIN_LENGTH = 1555;
    const MIN_PASSWORD_LENGTH = 5;
    const MAX_PASSWORD_LENGTH = 100;

    public function __construct(
        public ?int $id = null,
        public ?string $username = null,
        public ?string $email = null,
        public ?string $password_hash = null,
        public ?string $auth_key = null,
        public int $status = 10,
        public ?string $createdAt = null,
        public ?string $updatedAt = null
    ) {
        parent::__construct($id);
    }

    public function validatePassword(string $password): bool
    {
        return password_verify($password, $this->password_hash);
    }

    public function setPassword(string $password): void
    {
        $this->password_hash = password_hash($password, PASSWORD_DEFAULT);
    }

    public function generateAuthKey(): void
    {
        $this->auth_key = bin2hex(random_bytes(16));
    }

    public function getId(): ?string
    {
        return (string)$this->id;
    }
}
