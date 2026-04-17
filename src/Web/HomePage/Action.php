<?php

declare(strict_types=1);

namespace App\Web\HomePage;

use App\Domain\Event\Repository\EventRepository;
use App\Domain\User\Repository\TelegramUserRepository;
use App\Web\Telegram\UserHandler;
use danog\MadelineProto\EventHandler\Attributes\Handler;
use HttpSoft\Message\ResponseFactory;
use Psr\Http\Message\ResponseInterface;
use Yiisoft\Db\Connection\ConnectionInterface;
use Yiisoft\Json\Json;
use Yiisoft\User\CurrentUser;
use Yiisoft\Yii\View\Renderer\ViewRenderer;
use Yiisoft\Yii\View\Renderer\WebViewRenderer;

final readonly class Action
{
    public function __construct(
        private WebViewRenderer $viewRenderer,
        private CurrentUser $currentUser,
        private ResponseFactory $responseFactory,
    ) {
    }

    public function __invoke(): ResponseInterface
    {
        if ($this->currentUser->isGuest()) {
            return $this->responseFactory->createResponse()->withStatus(302)->withHeader('Location', '/login');
        }

        return $this->viewRenderer->render(__DIR__ . '/template');
    }
}
