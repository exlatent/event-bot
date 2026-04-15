<?php

declare(strict_types=1);


namespace App\Web\Admin\Message\Index;

use App\Domain\Telegram\Repository\MessageRepository;
use App\Domain\Telegram\Repository\SourceRepository;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Yiisoft\Data\Paginator\OffsetPaginator;
use Yiisoft\Data\Reader\Iterable\IterableDataReader;
use Yiisoft\Db\Connection\ConnectionInterface;
use Yiisoft\Http\Method;
use Yiisoft\Json\Json;
use Yiisoft\Router\FastRoute\UrlGenerator;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Yii\View\Renderer\ViewRenderer;

final readonly class Action
{
    public function __construct(
        private ViewRenderer $viewRenderer,
        private ConnectionInterface $connection,
        private UrlGeneratorInterface $url,
        private ResponseFactoryInterface $responseFactory
    ) {

    }

    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $page = (int) ($request->getQueryParams()['page'] ?? 1);

        $data = (new MessageRepository($this->connection))->findAll('id DESC');
        $reader = new IterableDataReader($data);

        $paginator = (new OffsetPaginator($reader))
            ->withPageSize(20)
            ->withCurrentPage($page);

        if ($request->getMethod() === Method::POST) {
            $selected = Json::decode($request->getParsedBody()['selected'] ?? []);
            foreach ($selected as $id) {
                $repo = new MessageRepository($this->connection);
                if($model = $repo->findOne(['id' => (int)$id])) {
                    $repo->delete($model);
                }
            }

            return $this->responseFactory->createResponse()->withStatus(302)->withHeader('Location',
                '/admin/message?page=' . $page);
        }

        return $this->viewRenderer
            ->withViewPath(__DIR__)
            ->render('template', [
                'data' => $paginator,
                'url' => $this->url,
                'source_repository' => (new SourceRepository($this->connection)),
            ]);
    }
}
