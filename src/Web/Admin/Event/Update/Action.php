<?php

declare(strict_types=1);


namespace App\Web\Admin\Event\Update;

use App\Domain\Event\Event;
use App\Domain\Event\Repository\EventRepository;
use App\Shared\ApplicationDateTime;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Yiisoft\Db\Connection\ConnectionInterface;
use Yiisoft\FormModel\FormHydrator;
use Yiisoft\Http\Method;
use Yiisoft\Router\HydratorAttribute\RouteArgument;
use Yiisoft\Session\Flash\Flash;
use Yiisoft\Yii\View\Renderer\WebViewRenderer;

final readonly class Action
{
    public function __construct(
        private WebViewRenderer $viewRenderer,
        private ConnectionInterface $connection,
        private FormHydrator $formHydrator,
        private ResponseFactoryInterface $responseFactory,
        private Flash $flash,
    ) {

    }

    public function __invoke(
        #[RouteArgument('id')]
        int $id,
        ServerRequestInterface $request,
    ): ResponseInterface {
        $repo = new EventRepository($this->connection);
        $model = $repo->findOne(['id' => $id]);

        if ($model === null) {
            throw new \Exception('Message not found');
        }
        /** @var Event $model */
        $form = new Form($model);
        if ($request->getMethod() === Method::POST) {
            if ($this->formHydrator->populateFromPostAndValidate($form, $request)) {
                $model->title = $form->title;
                $model->location = $form->location;
                $model->price = $form->price;
                $model->state = $form->state;
                $model->datetime = ApplicationDateTime::toDb(ApplicationDateTime::fromInput($form->datetime));
                $model->updatedAt = ApplicationDateTime::toDb(ApplicationDateTime::now());
                $repo->save($model);

                return $this->responseFactory->createResponse()->withStatus(302)->withHeader('Location',
                    '/admin/event');
            }
        }

        return $this->viewRenderer
            ->withViewPath(__DIR__)
            ->render('template', [
                'model' => $model,
                'form'  => $form,
                'flash' => $this->flash
            ]);
    }
}
