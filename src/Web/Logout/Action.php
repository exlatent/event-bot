<?php

declare(strict_types=1);


namespace App\Web\Logout;

use HttpSoft\Message\ResponseFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Yiisoft\User\CurrentUser;

class Action
{
    public function __construct(
        private ResponseFactory $responseFactory,
        private CurrentUser $currentUser,

    ) {
    }

    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        if (!$this->currentUser->isGuest()) {
            $this->currentUser->logout();
        }

        return $this->responseFactory->createResponse()->withStatus(302)->withHeader('Location', '/');
    }

}
