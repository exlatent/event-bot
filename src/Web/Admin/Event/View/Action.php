<?php

declare(strict_types=1);


namespace App\Web\Admin\Event\View;

use App\Domain\Event\Repository\EventRepository;
use App\Domain\Telegram\Message;
use App\Domain\Telegram\Repository\MessageRepository;
use App\Domain\Telegram\Repository\SourceRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Yiisoft\Db\Connection\ConnectionInterface;
use Yiisoft\Router\HydratorAttribute\RouteArgument;
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

    public function __invoke(
        #[RouteArgument('id')]
        int $id,
        ServerRequestInterface $request
    ): ResponseInterface {
        $repo = new EventRepository($this->connection);
        $model = $repo->findOne(['id' => $id]);

        if ($model === null) {
            throw new \Exception('Event not found');
        }

        /** @var Message $model */

        return $this->viewRenderer
            ->withViewPath(__DIR__)
            ->render('template', [
                'model' => $model,
                'url'   => $this->url,
            ]);
    }
}
