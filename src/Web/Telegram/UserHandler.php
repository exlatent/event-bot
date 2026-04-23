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
    public function extractUser($data): void
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

    public function sync($data, $new_status = null): void
    {
        if(!$data) {
            exit();
        }

        $now = ApplicationDateTime::toDb(ApplicationDateTime::now());
        $user = $this->users->findOne(['tg_id' => $data['id']]);
        if (!$user) {
            $user = new TelegramUser(
                tg_id: $data['id'],
                username: $data['username'] ?? null,
                first_name: $data['first_name'] ?? null,
                last_name: $data['last_name'] ?? null,
                language_code: $data['language_code'] ?? null,
                status: TelegramUser::STATUS_ACTIVE,
                createdAt: $now,
                updatedAt: $now,
                lastActivity: $now
            );
        } else {
            $user->username = $data['username'] ?? null;
            $user->lastActivity = $now;
            $user->status = TelegramUser::STATUS_ACTIVE;

            if($new_status === 'kicked') {
                $user->status = TelegramUser::STATUS_INACTIVE;
            }
        }

        $this->users->save($user);
    }
}
