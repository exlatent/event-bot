<?php

declare(strict_types=1);

namespace App\Migration;

use Yiisoft\Db\Migration\MigrationBuilder;
use Yiisoft\Db\Migration\RevertibleMigrationInterface;

final class M260319121512User implements RevertibleMigrationInterface
{
    public function up(MigrationBuilder $b): void
    {
        $c = $b->columnBuilder();

        if ($b->getDb()->getTableSchema('user') === null) {
            $b->createTable('user', [
                'id'            => $c::primaryKey(),
                'username'      => $c::string()->notNull()->unique(),
                'email'         => $c::string()->notNull()->unique(),
                'password_hash' => $c::string()->notNull(),
                'auth_key'      => $c::string(32),
                'status'        => $c::tinyint()->notNull()->defaultValue(10),
                'createdAt'     => $c::timestamp()->notNull(),
                'updatedAt'     => $c::timestamp()->notNull(),
            ]);
        }
    }

    public function down(MigrationBuilder $b): void
    {
        if ($b->getDb()->getTableSchema('user') !== null) {
            $b->dropTable('user');
        }
    }
}
