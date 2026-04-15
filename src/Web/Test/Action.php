<?php

namespace App\Web\Test;

use App\Domain\Rbac\Items\SomePermission;
use Yiisoft\Rbac\Db\AssignmentsStorage;
use Yiisoft\Rbac\Db\ItemsStorage;
use Yiisoft\Yii\View\Renderer\ViewRenderer;

final readonly class Action
{
    public function __construct(
        private ViewRenderer $viewRenderer,
        private ItemsStorage $itemStorage,
        private AssignmentsStorage $asStorage
//        private CurrentUser $currentUser,
//        private TelegramClient $client,
//        private ConnectionInterface $connection
    )
    {
    }


}
