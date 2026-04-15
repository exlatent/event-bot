<?php

/** @var Event $model */
/** @var \App\Web\Admin\Event\Update\Form $form */
/** @var \Yiisoft\Session\Flash\Flash $flash */

/** @var string $csrf */


use App\Domain\Event\Event;
use Yiisoft\Html\Html;

?>

<main>
    <div class="container">
        <h2>Update Event: <?= $model->title ?></h2>
        <?php if ($flash->has('error')) : ?>
            <div class="alert alert-danger">
                <?= $flash->get('error') ?>
            </div>
        <?php endif; ?>

        <?= Html::form()
            ->post()
            ->action('/admin/event/update/' . $model->id)
            ->csrf($csrf)
            ->open()
        ?>

        <div class="form-group mb-3 w-50">
            <label for="title" class="form-label">Title</label>
            <?= Html::textInput('UpdateForm[title]', $model->title, [
                'class' => 'form-control',
            ]) ?>
        </div>
        <div class="form-group mb-3 w-50">
            <label for="datetime" class="form-label">Date and Time</label>
            <?= Html::textInput('UpdateForm[datetime]', $model->datetime, [
                'type'  => 'datetime-local',
                'class' => 'form-control',
            ]) ?>
        </div>
        <div class="form-group mb-3 w-50">
            <label for="location" class="form-label">Location</label>
            <?= Html::textInput('UpdateForm[location]', $model->location, [
                'class' => 'form-control',
            ]) ?>
        </div>
        <div class="form-group mb-3 w-50">
            <label for="price" class="form-label">Price</label>
            <?= Html::textInput('UpdateForm[price]', $model->price, [
                'class' => 'form-control',
            ]) ?>
        </div>
        <div class="form-group mb-3 w-50">
            <label for="state" class="form-label">State</label>
            <select name="UpdateForm[state]" id="state" class="form-control">
                <?php foreach (Event::$states as $index => $state) : ?>
                    <?= Html::option($state, $index)->addAttributes([
                        'selected' => $index === $model->state,
                    ]) ?>
                <?php endforeach; ?>
            </select>
        </div>


        <?= Html::submitButton('Save')->addAttributes(['class' => 'btn btn-primary mt-3']) ?>

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





