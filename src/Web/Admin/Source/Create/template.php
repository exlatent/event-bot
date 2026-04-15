<?php

/** @var string $csrf */

/** @var \Yiisoft\Session\Flash\Flash $flash */

use Yiisoft\Html\Html;

?>

<main>
    <div class="container">
        <h2>Add Source</h2>
        <?php if ($flash->has('error')) : ?>
            <div class="alert alert-danger">
                <?= $flash->get('error') ?>
            </div>
        <?php endif; ?>

        <?= Html::form()
            ->post()
            ->action('/admin/source/create')
            ->csrf($csrf)
            ->open()
        ?>

        <div class="input-group mb-3 w-50">
            <span class="input-group-text">@</span>
            <?= Html::textInput('username', '', [
                'class' => 'form-control',
            ]) ?>
        </div>


        <?= Html::submitButton('Add')->addAttributes(['class' => 'btn btn-primary mt-3']) ?>

        <?= Html::form()->close() ?>
    </div>
</main>






