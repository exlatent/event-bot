<?php

declare(strict_types=1);

namespace App\Migration;

use App\Domain\Rbac\AdminPanelPermission;
use App\Domain\Rbac\AdminRole;
use Yiisoft\Db\Migration\MigrationBuilder;
use Yiisoft\Db\Migration\RevertibleMigrationInterface;
use Yiisoft\Rbac\Db\ItemsStorage;
use Yiisoft\Rbac\Manager;
use Yiisoft\Rbac\ManagerInterface;

final readonly class M260319123025Rbac implements RevertibleMigrationInterface
{
    public function __construct(private ManagerInterface $manager)
    {
    }

    public function up(MigrationBuilder $b): void
    {
        $c = $b->columnBuilder();

        if ($b->getDb()->getTableSchema('yii_rbac_item') === null) {
            $b->createTable('yii_rbac_item', [
                'name'        => $c::string()->primaryKey(),
                'description' => $c::text(),
                'rule_name'   => $c::string(),
                'type'        => $c::string()->notNull(),
                'created_at'  => $c::integer(),
                'updated_at'  => $c::integer()
            ]);
        }

        if ($b->getDb()->getTableSchema('yii_rbac_item_child') === null) {
            $b->createTable('yii_rbac_item_child', [
                'parent' => $c::string(64)->notNull(),
                'child'  => $c::string(64)->notNull()
            ]);

            $b->addPrimaryKey('yii_rbac_item_child', 'yii_rbac_item_child_pk', ['parent', 'child']);
        }

        if ($b->getDb()->getTableSchema('yii_rbac_assignment') === null) {
            $b->createTable('yii_rbac_assignment', [
                'item_name'   => $c::string(64)->notNull(),
                'user_id'     => $c::integer()->notNull(),
                'description' => $c::text(),
                'created_at'  => $c::integer(),
            ]);

            $b->addPrimaryKey('yii_rbac_assignment', 'yii_rbac_assignment_pk', ['item_name', 'user_id']);
        }

        $storage = new ItemsStorage($b->getDb());
        $admin_role = (new AdminRole(AdminRole::ROLE_NAME))
            ->withCreatedAt(time())
            ->withUpdatedAt(time());
        $storage->add($admin_role);

        $admin_perm = (new AdminPanelPermission(AdminPanelPermission::PERM_NAME))
            ->withDescription(AdminPanelPermission::PERM_DESCRIPTION)
            ->withCreatedAt(time())
            ->withUpdatedAt(time());
        $storage->add($admin_perm);

        $this->manager->addChild($admin_role->getName(), $admin_perm->getName());


    }

    public function down(MigrationBuilder $b): void
    {
        if ($b->getDb()->getTableSchema('yii_rbac_item') !== null) {
            $b->dropTable('yii_rbac_item');
        }

        if ($b->getDb()->getTableSchema('yii_rbac_item_child')) {
            $b->dropTable('yii_rbac_item_child');
        }

        if ($b->getDb()->getTableSchema('yii_rbac_assignment') !== null) {
            $b->dropTable('yii_rbac_assignment');
        }
    }
}
