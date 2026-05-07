<?php

declare(strict_types=1);


namespace App\Web\Admin\Source\Index;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Yii\View\Renderer\WebViewRenderer;

final readonly class Action
{
    public function __construct(
        private WebViewRenderer $viewRenderer,
        private UrlGeneratorInterface $url,
        private DataReader $dataReader
    ) {

    }

    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        return $request
            ->getAttribute(WebViewRenderer::class, $this->viewRenderer)
            ->withViewPath(__DIR__)
            ->render('template', [
                'data' => $this->dataReader,
                'url' => $this->url,
            ]);
    }
}
