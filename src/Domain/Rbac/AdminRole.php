<?php

declare(strict_types=1);


namespace App\Domain\Rbac;

use Yiisoft\Rbac\Item;

class AdminRole extends Item
{
    const ROLE_NAME = 'admin';

    public function getType(): string
    {
        return Item::TYPE_ROLE;
    }
}
