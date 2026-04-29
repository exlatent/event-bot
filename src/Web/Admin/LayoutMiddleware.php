<?php

declare(strict_types=1);


namespace App\Web\Admin;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Yiisoft\Yii\View\Renderer\WebViewRenderer;

final readonly class LayoutMiddleware implements MiddlewareInterface
{
    public function __construct(
        private WebViewRenderer $viewRenderer
    ) {}

    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        $renderer = $this->viewRenderer->withLayout('@src/Web/Shared/Layout/Admin/layout.php');
        return $handler->handle($request->withAttribute(WebViewRenderer::class, $renderer));
    }
}
