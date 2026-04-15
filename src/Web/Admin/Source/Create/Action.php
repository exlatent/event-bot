<?php

declare(strict_types=1);


namespace App\Web\Admin\Source\Create;

use App\Api\Telegram\TelegramClient;
use App\Domain\Rbac\AdminPanelPermission;
use App\Domain\Telegram\Repository\SourceRepository;
use App\Domain\Telegram\Source;
use App\Infrastructure\CustomOffsetPaginator;
use App\Infrastructure\DataReader;
use danog\MadelineProto\Exception;
use HttpSoft\Message\ResponseFactory;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Yiisoft\Data\Paginator\OffsetPaginator;
use Yiisoft\Data\Reader\Iterable\IterableDataReader;
use Yiisoft\Db\Connection\ConnectionInterface;
use Yiisoft\FormModel\FormHydrator;
use Yiisoft\Http\Method;
use Yiisoft\Router\HydratorAttribute\RouteArgument;
use Yiisoft\Session\Flash\Flash;
use Yiisoft\Session\Session;
use Yiisoft\User\CurrentUser;
use Yiisoft\Yii\View\Renderer\ViewRenderer;
use Yiisoft\Yii\DataView\Pagination\OffsetPagination;
use danog\MadelineProto\RPCErrorException;

final readonly class Action
{
    public function __construct(
        private ViewRenderer $viewRenderer,
        private ConnectionInterface $connection,
        private ResponseFactoryInterface $responseFactory,
        private TelegramClient $client,
        private Flash $flash
    ) {

    }

    public function __invoke(
        ServerRequestInterface $request,
    ): ResponseInterface {
        $repo = new SourceRepository($this->connection);
        $model = new Source();
        /** @var Source $model */
        if ($request->getMethod() === Method::POST) {
            try {
                $username = $request->getParsedBody()['username'] ?? '';
                if ($source = $this->getTgSource($username)) {
                    $model->username = $username;
                    $model->title = $source['title'] ?? '';
                    $model->tg_id = (int) $source['id'] ?? '';
                    $model->createdAt = date('Y-m-d H:i:s');
                    $model->updatedAt = date('Y-m-d H:i:s');
                    $repo->save($model);

                    return $this->responseFactory->createResponse()->withStatus(302)->withHeader('Location',
                        '/admin/source');
                } else {
                    $this->flash->set('error', 'Telegram channel not found');
                }
            } catch (RPCErrorException $e) {
                $this->flash->set('error', $e->getMessage());
            }

        }

        return $this->viewRenderer
            ->withViewPath(__DIR__)
            ->render('template',
                ['flash' => $this->flash]
            );
    }

    private function getTgSource(string $username): ?array
    {
        $api = $this->client->getApi();
        $info = $api->getInfo("@$username");
        return $info['Chat'] ?? null;
    }
}
