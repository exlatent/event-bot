<?php

declare(strict_types=1);


namespace App\Web\Admin\Event\Index;

use App\Domain\Event\Repository\EventRepository;
use App\Domain\Telegram\Repository\MessageRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Yiisoft\Data\Paginator\OffsetPaginator;
use Yiisoft\Data\Reader\Iterable\IterableDataReader;
use Yiisoft\Db\Connection\ConnectionInterface;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Yii\View\Renderer\WebViewRenderer;

final readonly class Action
{
    public function __construct(
        private WebViewRenderer $viewRenderer,
        private ConnectionInterface $connection,
        private UrlGeneratorInterface $url,
        private EventRepository $eventRepository
    ) {

    }

    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $page = (int) ($request->getQueryParams()['page'] ?? 1);
        $filter = new SearchForm();
        $queryParams = $request->getQueryParams();
        $filter->load($queryParams);
        $data = $this->eventRepository->findByFilter($filter);
        $reader = new IterableDataReader($data);

        $paginator = (new OffsetPaginator($reader))
            ->withPageSize(20)
            ->withCurrentPage($page);

        return
            $request
                ->getAttribute(WebViewRenderer::class, $this->viewRenderer)
                ->withViewPath(__DIR__)
                ->render('template', [
                    'filter'             => $filter,
                    'data'               => $paginator,
                    'url'                => $this->url,
                    'message_repository' => (new MessageRepository($this->connection)),
                ]);
    }
}
