<?php

declare(strict_types=1);


namespace App\Web\Admin\Index;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Yiisoft\Yii\View\Renderer\WebViewRenderer;

class Action
{
    public function __construct(
        private WebViewRenderer $viewRenderer,
    ) {

    }

    public function __invoke(
        ServerRequestInterface $request,
    ): ResponseInterface {
        return
            $request
                ->getAttribute(WebViewRenderer::class, $this->viewRenderer)
                ->withViewPath(__DIR__)
            ->render('template');

    }
}
