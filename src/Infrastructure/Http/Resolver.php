<?php

declare(strict_types=1);


namespace App\Infrastructure\Http;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Yiisoft\Csrf\CsrfTokenMiddleware;
use Yiisoft\DataResponse\Middleware\FormatDataResponse;
use Yiisoft\ErrorHandler\Middleware\ErrorCatcher;
use Yiisoft\Middleware\Dispatcher\MiddlewareDispatcher;
use Yiisoft\Middleware\Dispatcher\MiddlewareFactory;
use Yiisoft\RequestProvider\RequestCatcherMiddleware;
use Yiisoft\Router\Middleware\Router;
use Yiisoft\Session\SessionMiddleware;

final readonly class Resolver implements MiddlewareInterface
{

    public function __construct(
        private MiddlewareFactory $factory
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $path = $request->getUri()->getPath();

        $isApi = str_starts_with($path, '/telegram')
            || str_starts_with($path, '/api');

        if ($isApi) {
            $middlewares = [
                ErrorCatcher::class,
                Router::class,
            ];
        } else {
            $middlewares = [
                ErrorCatcher::class,
                SessionMiddleware::class,
                CsrfTokenMiddleware::class,
                FormatDataResponse::class,
                RequestCatcherMiddleware::class,
                Router::class,
            ];
        }

        $dispatcher = new MiddlewareDispatcher($this->factory);
        $dispatcher = $dispatcher->withMiddlewares($middlewares);

        return $dispatcher->dispatch($request, $handler);
    }
}
