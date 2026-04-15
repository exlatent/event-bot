<?php

declare(strict_types=1);


namespace App\Web\Admin\Source\Update;

use App\Domain\Rbac\AdminPanelPermission;
use App\Domain\Telegram\Repository\SourceRepository;
use App\Domain\Telegram\Source;
use App\Infrastructure\CustomOffsetPaginator;
use App\Infrastructure\DataReader;
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
use Yiisoft\User\CurrentUser;
use Yiisoft\Yii\View\Renderer\ViewRenderer;
use Yiisoft\Yii\DataView\Pagination\OffsetPagination;

final readonly class Action
{
    public function __construct(
        private ViewRenderer $viewRenderer,
        private ConnectionInterface $connection,
        private FormHydrator $formHydrator,
        private ResponseFactoryInterface $responseFactory
    ) {

    }

    public function __invoke(
        #[RouteArgument('id')]
        int $id,
        ServerRequestInterface $request,
    ): ResponseInterface {
        $repo = new SourceRepository($this->connection);
        $model = $repo->findOne(['id' => $id]);

        if ($model === null) {
            throw new \Exception('Source not found');
        }
        /** @var Source $model */
        $form = new Form($model);
        if ($request->getMethod() === Method::POST) {
                if ($this->formHydrator->populateFromPostAndValidate($form, $request, null, false)) {
                $model->username = $form->username;
                $model->title = $form->title;
                $model->is_active = (bool)$form->is_active;
                $model->updatedAt = date('Y-m-d H:i:s');
                $repo->save($model);

                return $this->responseFactory->createResponse()->withStatus(302)->withHeader('Location',
                    '/admin/source');
            } else {
                var_dump($form->getFormName());
                exit();
            }
        }

        return $this->viewRenderer
            ->withViewPath(__DIR__)
            ->render('template', [
                'model' => $model,
                'form'  => $form,
            ]);
    }
}
