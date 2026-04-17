<?php

declare(strict_types=1);

namespace App\Migration;

use Yiisoft\Db\Migration\MigrationBuilder;
use Yiisoft\Db\Migration\RevertibleMigrationInterface;

final class M260417121006TelegramUser implements RevertibleMigrationInterface
{
    public function up(MigrationBuilder $b): void
    {
        $c = $b->columnBuilder();

        if ($b->getDb()->getTableSchema('telegram_user') === null) {
            $b->createTable('telegram_user', [
                'id'            => $c::primaryKey(),
                'tg_id'         => $c::bigint()->notNull()->unique(),
                'username'      => $c::string()->notNull()->unique(),
                'first_name'    => $c::string(),
                'last_name'     => $c::string(),
                'language_code' => $c::string(),
                'status'        => $c::tinyint()->notNull()->defaultValue(10),
                'createdAt'     => $c::datetime()->notNull(),
                'updatedAt'     => $c::datetime()->notNull(),
                'lastActivity'  => $c::datetime()->notNull(),
            ]);
        }
    }

    public function down(MigrationBuilder $b): void
    {
        if ($b->getDb()->getTableSchema('telegram_user') !== null) {
            $b->dropTable('telegram_user');
        }
    }
}
