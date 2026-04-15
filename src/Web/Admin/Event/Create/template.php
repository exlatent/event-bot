<?php

/** @var string $csrf */

/** @var \Yiisoft\Session\Flash\Flash $flash */

use Yiisoft\Html\Html;

?>

<main>
    <div class="container">
        <h2>Add Event</h2>
        <?php if ($flash->has('error')) : ?>
            <div class="alert alert-danger">
                <?= $flash->get('error') ?>
            </div>
        <?php endif; ?>

        <?= Html::form()
            ->post()
            ->action('/admin/event/create')
            ->csrf($csrf)
            ->open()
        ?>

        <div class="form-group mb-3 w-50">
            <label for="title" class="form-label">Title</label>
            <?= Html::textInput('CreateForm[title]', '', [
                'class' => 'form-control',
            ]) ?>
        </div>
        <div class="form-group mb-3 w-50">
            <label for="datetime" class="form-label">Date and Time</label>
            <?= Html::textInput('CreateForm[datetime]', '', [
                'type'  => 'datetime-local',
                'class' => 'form-control',
            ]) ?>
        </div>
        <div class="form-group mb-3 w-50">
            <label for="location" class="form-label">Location</label>
            <?= Html::textInput('CreateForm[location]', '', [
                'class' => 'form-control',
            ]) ?>
        </div>
        <div class="form-group mb-3 w-50">
            <label for="price" class="form-label">Price</label>
            <?= Html::textInput('CreateForm[price]', '', [
                'class' => 'form-control',
            ]) ?>
        </div>
        <div class="form-group mb-3 w-50">
            <label for="state" class="form-label">State</label>
            <select name="CreateForm[state]" id="state" class="form-control">
                <?php foreach (\App\Domain\Event\Event::$states as $index => $state) : ?>
                    <?= Html::option($state, $index) ?>
                <?php endforeach; ?>
            </select>
        </div>


        <?= Html::submitButton('Add')->addAttributes(['class' => 'btn btn-primary mt-3']) ?>

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
</main>






