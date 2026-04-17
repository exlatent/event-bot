<?php

declare(strict_types=1);

namespace App\Migration;

use Yiisoft\Db\Migration\MigrationBuilder;
use Yiisoft\Db\Migration\RevertibleMigrationInterface;

final class M260417080653AlterTimestampColumns implements RevertibleMigrationInterface
{
    public function up(MigrationBuilder $b): void
    {
        // Event
        $b->alterColumn('event', 'createdAt', 'datetime NOT NULL');
        $b->alterColumn('event', 'updatedAt', 'datetime NOT NULL');
        $b->alterColumn('event', 'datetime', 'datetime NOT NULL');
        $b->alterColumn('event', 'lastCheckedAt', 'datetime');

        // Message
        $b->alterColumn('message', 'date', 'datetime NOT NULL');
        $b->alterColumn('message', 'createdAt', 'datetime NOT NULL');
        $b->alterColumn('message', 'analyzedAt', 'datetime');
        $b->alterColumn('message', 'processedAt', 'datetime');

        // Source
        $b->alterColumn('source', 'createdAt', 'datetime NOT NULL');
        $b->alterColumn('source', 'updatedAt', 'datetime NOT NULL');

        //User
        $b->alterColumn('user', 'createdAt', 'datetime NOT NULL');
        $b->alterColumn('user', 'updatedAt', 'datetime NOT NULL');


    }

    public function down(MigrationBuilder $b): void
    {
        // Event
        $b->alterColumn('event', 'createdAt', 'timestamp NOT NULL');
        $b->alterColumn('event', 'updatedAt', 'timestamp NOT NULL');
        $b->alterColumn('event', 'datetime', 'timestamp NOT NULL');
        $b->alterColumn('event', 'lastCheckedAt', 'timestamp');

        // Message
        $b->alterColumn('message', 'createdAt', 'timestamp NOT NULL');
        $b->alterColumn('message', 'date', 'timestamp NOT NULL');
        $b->alterColumn('message', 'analyzedAt', 'timestamp');
        $b->alterColumn('message', 'processedAt', 'timestamp');

        // Source
        $b->alterColumn('source', 'createdAt', 'timestamp NOT NULL');
        $b->alterColumn('source', 'updatedAt', 'timestamp NOT NULL');

        //User
        $b->alterColumn('user', 'createdAt', 'timestamp NOT NULL');
        $b->alterColumn('user', 'updatedAt', 'timestamp NOT NULL');

    }
}
