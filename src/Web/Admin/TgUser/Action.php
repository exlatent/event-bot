<?php

declare(strict_types=1);


namespace App\Web\Admin\TgUser;

use App\Domain\User\Repository\TelegramUserRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Yiisoft\Data\Paginator\OffsetPaginator;
use Yiisoft\Data\Reader\Iterable\IterableDataReader;
use Yiisoft\Yii\View\Renderer\WebViewRenderer;

final readonly class Action
{
    public function __construct(
        private WebViewRenderer $viewRenderer,
        private TelegramUserRepository $repository
    ) {

    }

    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $page = (int) ($request->getQueryParams()['page'] ?? 1);

        $data = $this->repository->findAll();
        $reader = new IterableDataReader($data);

        $paginator = (new OffsetPaginator($reader))
            ->withPageSize(20)
            ->withCurrentPage($page);

        return $request
            ->getAttribute(WebViewRenderer::class, $this->viewRenderer)
            ->withViewPath(__DIR__)
            ->render('template', [
                'data' => $paginator
            ]);
    }
}
