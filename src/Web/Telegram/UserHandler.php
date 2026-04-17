<?php

declare(strict_types=1);


namespace App\Web\Telegram;

use App\Domain\User\Repository\TelegramUserRepository;
use App\Domain\User\TelegramUser;
use App\Shared\ApplicationDateTime;

final class UserHandler
{
    private ?array $user = null;

    public function __construct(
        private readonly TelegramUserRepository $users
    ) {
    }

    /**
     * @param $update
     * @return void
     */
    public function extractUser($update): void
    {
        if (isset($update['message']['from'])) {
            $this->user = $update['message']['from'];
        }

        if (isset($update['callback_query']['from'])) {
            $this->user = $update['callback_query']['from'];
        }

        if (isset($update['my_chat_member']['from'])) {
            $this->user = $update['my_chat_member']['from'];
        }
    }

    public function sync($update): void
    {
        $this->extractUser($update);
        if(!$this->user) {
            exit();
        }
        $now = ApplicationDateTime::toDb(ApplicationDateTime::now());
        $user = $this->users->findOne(['tg_id' => $this->user['id']]);
        if (!$user) {
            $user = new TelegramUser(
                tg_id: $this->user['id'],
                username: $this->user['username'] ?? null,
                first_name: $this->user['first_name'] ?? null,
                last_name: $this->user['last_name'] ?? null,
                language_code: $this->user['language_code'] ?? null,
                status: TelegramUser::STATUS_ACTIVE,
                createdAt: $now,
                updatedAt: $now,
                lastActivity: $now
            );
        } else {
            $user->username = $this->user['username'] ?? null;
            $user->lastActivity = $now;
            $user->status = TelegramUser::STATUS_ACTIVE;

            $kicked = isset($update['my_chat_member']['new_chat_member']['status'])
                && $update['my_chat_member']['new_chat_member']['status'] === 'kicked';

            if($kicked) {
                $user->status = TelegramUser::STATUS_INACTIVE;
            }
        }

        $this->users->save($user);
    }
}
