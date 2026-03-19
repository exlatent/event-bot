<?php

declare(strict_types=1);

namespace App\Migration;

use Yiisoft\Db\Migration\MigrationBuilder;
use Yiisoft\Db\Migration\RevertibleMigrationInterface;

final class M260318122403MessageProcessedColumn implements RevertibleMigrationInterface
{
    public function up(MigrationBuilder $b): void
    {
        $c = $b->columnBuilder();
        $b->addColumn('message', 'processedAt', $c::timestamp()->null());
    }

    public function down(MigrationBuilder $b): void
    {
        $b->dropColumn('message', 'processedAt');
    }
}
