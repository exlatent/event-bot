<?php

declare(strict_types=1);

namespace App\Migration;

use Yiisoft\Db\Migration\MigrationBuilder;
use Yiisoft\Db\Migration\RevertibleMigrationInterface;

final class M260316133050Messages implements RevertibleMigrationInterface
{
    public function up(MigrationBuilder $b): void
    {
        $c = $b->columnBuilder();

        if (!$b->getDb()->getTableSchema('message')) {
            $b->createTable('message', [
                'id'              => $c::primaryKey(),
                'source_id'       => $c::integer()->notNull(),
                'tg_id'           => $c::bigint()->notNull(),
                'source_tg_id'    => $c::bigint()->notNull(),
                'message'         => $c::text()->notNull(),
                'date'            => $c::timestamp()->notNull(),
                'createdAt'       => $c::timestamp()->notNull(),
                'analyzedAt'      => $c::timestamp(),
                'event_candidate' => $c::tinyint(),
                'spam'            => $c::tinyint(),
                'off_topic'       => $c::tinyint(),
                'confidence'      => $c::float(),

            ], 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci');
        }
    }

    public function down(MigrationBuilder $b): void
    {
        if ($b->getDb()->getTableSchema('message')) {
            $b->dropTable('message');
        }
    }
}
