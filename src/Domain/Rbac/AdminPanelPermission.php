<?php

declare(strict_types=1);


namespace App\Domain\Rbac;

use Yiisoft\Rbac\Item;

final class AdminPanelPermission extends Item
{
    const PERM_NAME = 'adminPanel';
    const PERM_DESCRIPTION = 'Admin full access';

    public function getType(): string
    {
        return Item::TYPE_PERMISSION;
    }
}
