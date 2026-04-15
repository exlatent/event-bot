<?php

declare(strict_types=1);


namespace App\Domain\Rbac;

use HttpSoft\Message\ResponseFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Yiisoft\User\CurrentUser;

class CheckAccess implements MiddlewareInterface
{
    public function __construct(
        private readonly CurrentUser $currentUser,
        private readonly ResponseFactory $responseFactory,
        private readonly string $permissionName,
    ) {
    }

    public static function definition($permission_name): array
    {
        return [
            'class' => self::class,
            '__construct()' => [
                'permissionName' => $permission_name,
            ],
        ];
    }


    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ($this->currentUser->can($this->permissionName)) {
            return $handler->handle($request);
        }

        return $this->responseFactory->createResponse(403, 'Forbidden');
    }
}
