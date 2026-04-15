<?php

declare(strict_types=1);


namespace App\Web\Login;

use App\Asset\BootstrapAsset;
use App\Domain\Identity\IdentityRepository;
use App\Domain\User\Repository\UserRepository;
use App\Domain\User\User;
use HttpSoft\Message\ResponseFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Yiisoft\Assets\AssetManager;
use Yiisoft\Db\Connection\ConnectionInterface;
use Yiisoft\Http\Method;
use Yiisoft\Security\PasswordHasher;
use Yiisoft\User\CurrentUser;
use Yiisoft\FormModel\FormHydrator;
use Yiisoft\Yii\View\Renderer\ViewRenderer;

final readonly class Action
{
    public function __construct(
        private ResponseFactory $responseFactory,
        private CurrentUser $currentUser,
        private FormHydrator $formHydrator,
        private ViewRenderer $viewRenderer,
        private AssetManager $assetManager,
        private IdentityRepository $identityRepository

    ) {
    }

    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        if (!$this->currentUser->isGuest()) {
            return $this->responseFactory->createResponse()->withStatus(302)->withHeader('Location', '/');
        }

        $form = new Form();

        if ($request->getMethod() === Method::POST) {

            if($this->formHydrator->populateFromPostAndValidate($form, $request))
            {
                $user = $this->identityRepository->findIdentityByUsername($form->login);
                $hasher = new PasswordHasher();
                /** @var User $user */
                if(!$user || !$hasher->validate($form->password, $user->password_hash)) {
                    $form->addError(Form::ERROR_MESSAGE);
                    return $this->renderForm($form);
                } else {
                    $this->currentUser->login($user);
                    return $this->responseFactory->createResponse()->withStatus(302)->withHeader('Location', '/');
                }
            }
        }

        return $this->renderForm($form);
    }

    private function renderForm(Form $form): ResponseInterface
    {
        $this->assetManager->register(BootstrapAsset::class);
        return $this->viewRenderer
            ->withLayout('@layoutSource/Auth/layout')
            ->withViewPath(__DIR__)
            ->render('template', compact('form'));
    }

}
