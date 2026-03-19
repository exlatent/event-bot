<?php

declare(strict_types=1);

namespace App\Migration;

use Yiisoft\Db\Migration\MigrationBuilder;
use Yiisoft\Db\Migration\RevertibleMigrationInterface;

final class M260316102847Source implements RevertibleMigrationInterface
{
    public function up(MigrationBuilder $b): void
    {
        $c = $b->columnBuilder();

        if (!$b->getDb()->getTableSchema('source')) {
            $b->createTable('source', [
                'id'              => $c::primaryKey(),
                'tg_id'           => $c::bigint()->notNull(),
                'username'        => $c::string(),
                'title'           => $c::string()->notNull(),
                'is_active'       => $c::boolean()->defaultValue(1),
                'score'           => $c::double()->defaultValue(0.00),
                'last_message_id' => $c::integer(),
                'createdAt'       => $c::timestamp()->notNull(),
                'updatedAt'       => $c::timestamp()->notNull(),
            ], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci');
        }
    }

    public function down(MigrationBuilder $b): void
    {
        if ($b->getDb()->getTableSchema('source')) {
            $b->dropTable('source');
        }
    }
}
