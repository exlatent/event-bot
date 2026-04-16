<?php

declare(strict_types=1);


namespace App\Web\Admin\Message\Update;

use App\Domain\Telegram\Message;
use App\Domain\Telegram\Repository\MessageRepository;
use App\Shared\ApplicationDateTime;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Yiisoft\Db\Connection\ConnectionInterface;
use Yiisoft\FormModel\FormHydrator;
use Yiisoft\Http\Method;
use Yiisoft\Router\HydratorAttribute\RouteArgument;
use Yiisoft\Yii\View\Renderer\WebViewRenderer;

final readonly class Action
{
    public function __construct(
        private WebViewRenderer $viewRenderer,
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
        $repo = new MessageRepository($this->connection);
        $model = $repo->findOne(['id' => $id]);

        if ($model === null) {
            throw new \Exception('Message not found');
        }
        /** @var Message $model */
        $form = new Form($model);
        if ($request->getMethod() === Method::POST) {
            if ($this->formHydrator->populateFromPostAndValidate($form, $request, null, false)) {
                $model->spam = $form->spam;
                $model->off_topic = $form->off_topic;
                $model->event_candidate = $form->event_candidate;
                $model->confidence = $form->confidence;
                $model->analyzedAt = ApplicationDateTime::toDb(ApplicationDateTime::now());
                $repo->save($model);

                return $this->responseFactory->createResponse()->withStatus(302)->withHeader('Location',
                    '/admin/message');
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
