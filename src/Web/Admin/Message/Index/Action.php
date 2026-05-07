<?php

declare(strict_types=1);


namespace App\Web\Admin\Message\Index;

use App\Domain\Telegram\Repository\MessageRepository;
use App\Domain\Telegram\Repository\SourceRepository;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Yiisoft\Db\Connection\ConnectionInterface;
use Yiisoft\Http\Method;
use Yiisoft\Json\Json;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Yii\View\Renderer\WebViewRenderer;

final readonly class Action
{
    public function __construct(
        private WebViewRenderer $viewRenderer,
        private UrlGeneratorInterface $url,
        private ResponseFactoryInterface $responseFactory,
        private MessageRepository $messageRepository,
        private SourceRepository $sourceRepository,
        private DataReader $dataReader
    ) {
    }

    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        if ($request->getMethod() === Method::POST) {
            $selected = Json::decode($request->getParsedBody()['selected'] ?? []);
            foreach ($selected as $id) {
                if ($model = $this->messageRepository->findOne(['id' => (int) $id])) {
                    $this->messageRepository->delete($model);
                }
            }

            return $this->responseFactory->createResponse()->withStatus(302)->withHeader('Location',
                '/admin/message');
        }

        return $request
            ->getAttribute(WebViewRenderer::class, $this->viewRenderer)
            ->withViewPath(__DIR__)
            ->render('template', [
                'data'              => $this->dataReader,
                'url'               => $this->url,
                'source_repository' => $this->sourceRepository,
            ]);
    }
}
