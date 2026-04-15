<?php

declare(strict_types=1);


namespace App\Web\Admin\Event\Index;

use App\Domain\Event\Repository\EventRepository;
use App\Domain\Telegram\Repository\MessageRepository;
use App\Domain\Telegram\Repository\SourceRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Yiisoft\Data\Paginator\OffsetPaginator;
use Yiisoft\Data\Reader\Iterable\IterableDataReader;
use Yiisoft\Db\Connection\ConnectionInterface;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Yii\View\Renderer\ViewRenderer;

final readonly class Action
{
    public function __construct(
        private ViewRenderer $viewRenderer,
        private ConnectionInterface $connection,
        private UrlGeneratorInterface $url
    ) {

    }

    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $page = (int) ($request->getQueryParams()['page'] ?? 1);

        $data = (new EventRepository($this->connection))->findAll('id DESC');
        $reader = new IterableDataReader($data);

        $paginator = (new OffsetPaginator($reader))
            ->withPageSize(20)
            ->withCurrentPage($page);

        return $this->viewRenderer
            ->withViewPath(__DIR__)
            ->render('template', [
                'data' => $paginator,
                'url' => $this->url,
                'message_repository' => (new MessageRepository($this->connection)),
            ]);
    }
}
