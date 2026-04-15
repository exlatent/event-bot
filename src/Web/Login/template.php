<?php

/** @var string $csrf */
/** @var \App\Web\Login\Form $form */

use Yiisoft\Html\Html;

$this->setTitle('Login');

?>

<main>
    <div class="container">
        <div class="card w-50 m-auto">
            <div class="card-header"><h1>Login</h1></div>
            <div class="card-body">
                <?= Html::form()
                    ->post()
                    ->action('/login')
                    ->csrf($csrf)
                    ->open()
                ?>
                <label for="LoginFormInputLogin" class="form-label">Login</label>
                <?= Html::textInput('LoginForm[login]', $form->login)->addAttributes([
                    'id' => 'LoginFormInputLogin',
                    'class' => 'form-control loginForm_login',
                    'placeholder' => '',
                    'required' => true,
                ]) ?>
                <label for="LoginFormInputPassword" class="form-label">Password</label>
                <?= Html::passwordInput('LoginForm[password]', $form->password)->addAttributes([
                    'id' => 'LoginFormInputPassword',
                    'class' => 'form-control loginForm_password',
                    'placeholder' => '',
                    'required' => true,
                ]) ?>

                <div class="form-check text-start my-3">
                    <?= Html::checkbox('LoginForm[rememberMe]')
                        ->class('form-check-input')
                        ->id('LoginFormCheckboxRememberMe')
                        ->checked($form->rememberMe) ?>
                    <label class="form-check-label" for="LoginFormCheckboxRememberMe">
                        Remember me
                    </label>
                </div>
                <button class="btn btn-primary w-100 py-2" type="submit">Sign in</button>

                <?php
                if ($form->isValidated() && !$form->isValid()) {
                    echo Html::div(
                        implode(
                            '<br>',
                            array_map(
                                Html::encode(...),
                                $form->getValidationResult()->getErrorMessages(),
                            ),
                        ),
                        ['class' => 'text-bg-danger p-3 mt-4'],
                    )->encode(false);
                }
                ?>

                <?= Html::form()->close() ?>
            </div>
        </div>
    </div>
</main>
