<?php

declare(strict_types=1);

namespace App\Migration;

use Yiisoft\Db\Migration\MigrationBuilder;
use Yiisoft\Db\Migration\RevertibleMigrationInterface;

final class M260318122823Event implements RevertibleMigrationInterface
{
    public function up(MigrationBuilder $b): void
    {
        $c = $b->columnBuilder();

        if (!$b->getDb()->getTableSchema('event')) {
            $b->createTable('event', [
                'id'              => $c::primaryKey(),
                'message_id'      => $c::integer()->notNull(),
                'title'           => $c::string()->notNull(),
                'datetime'        => $c::timestamp()->notNull(),
                'location'        => $c::string()->notNull(),
                'price'           => $c::string(),
                'state'           => $c::tinyint()->defaultValue(0),
                'duplicate_of_id' => $c::integer(),
                'lastCheckedAt'   => $c::timestamp(),
                'createdAt'       => $c::timestamp()->notNull(),
                'updatedAt'       => $c::timestamp()->notNull(),

            ]);
        }
    }

    public function down(MigrationBuilder $b): void
    {
        if ($b->getDb()->getTableSchema('event')) {
            $b->dropTable('event');
        }
    }
}
