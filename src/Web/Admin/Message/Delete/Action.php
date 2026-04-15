<?php

declare(strict_types=1);


namespace App\Web\Admin\Message\Delete;

use App\Domain\Telegram\Message;
use App\Domain\Telegram\Repository\MessageRepository;
use Yiisoft\Http\Method;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Yiisoft\Db\Connection\ConnectionInterface;
use Yiisoft\Router\HydratorAttribute\RouteArgument;

final class Action
{
    public function __construct(
        private ConnectionInterface $connection,
        private ResponseFactoryInterface $responseFactory
    ) {

    }

    public function __invoke(
        #[RouteArgument('id')]
        int $id,
        ServerRequestInterface $request,
    ): ResponseInterface {
        $repo = new MessageRepository($this->connection);
        $model = $repo->findOne(['id' => $id]);

        if ($model === null) {
            throw new \Exception('Message not found');
        }
        /** @var Message $model */
        if ($request->getMethod() === Method::POST) {
            $repo->delete($model);
            return $this->responseFactory->createResponse()->withStatus(302)->withHeader('Location',
                '/admin/message');
        }
    }
}
