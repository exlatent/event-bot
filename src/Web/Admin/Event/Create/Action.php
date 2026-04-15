<?php

declare(strict_types=1);


namespace App\Web\Admin\Event\Create;

use App\Domain\Event\Event;
use App\Domain\Event\Repository\EventRepository;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Yiisoft\Db\Connection\ConnectionInterface;
use Yiisoft\FormModel\FormHydrator;
use Yiisoft\Http\Method;
use Yiisoft\Session\Flash\Flash;
use Yiisoft\Yii\View\Renderer\ViewRenderer;

final readonly class Action
{
    public function __construct(
        private ViewRenderer $viewRenderer,
        private ConnectionInterface $connection,
        private ResponseFactoryInterface $responseFactory,
        private Flash $flash,
        private FormHydrator $formHydrator
    ) {

    }

    public function __invoke(
        ServerRequestInterface $request,
    ): ResponseInterface {
        $repo = new EventRepository($this->connection);
        $model = new Event();
        $form = new Form();

        /** @var Event $model */
        if ($request->getMethod() === Method::POST) {
            if ($this->formHydrator->populateFromPostAndValidate($form, $request)) {
                $model->title = $form->title;
                $model->datetime = date('Y-m-d H:i:s', strtotime($form->datetime));
                $model->location = $form->location;
                $model->price = $form->price;
                $model->state = $form->state;
                $model->createdAt = date('Y-m-d H:i:s');
                $model->updatedAt = date('Y-m-d H:i:s');
                $repo->save($model);

                return $this->responseFactory->createResponse()->withStatus(302)->withHeader('Location',
                    '/admin/event');
            }
        }

        return $this->viewRenderer
            ->withViewPath(__DIR__)
            ->render('template',
                [
                    'form'  => $form,
                    'flash' => $this->flash
                ]
            );
    }
}
