<?php

/** @var Source $model */
/** @var \App\Web\Admin\Message\Update\Form $form */

/** @var string $csrf */


use App\Domain\Telegram\Source;
use Yiisoft\Html\Html;

?>

<main>
    <div class="container">
        <h2>Update Source <?= $model->id ?></h2>

        <?php
        $field = new \Yiisoft\FormModel\FieldFactory();
        ?>
        <?= Html::form()
            ->post()
            ->action('/admin/message/update/'.$model->id)
            ->csrf($csrf)
            ->open()
        ?>

        <?= $field->checkbox($form, 'event_candidate')
            ->addInputAttributes(['class' => 'form-check-input mb-2']) ?>
        <?= $field->checkbox($form, 'spam')->addInputAttributes(['class' => 'form-check-input mb-2']) ?>
        <?= $field->checkbox($form, 'off_topic')->addInputAttributes(['class' => 'form-check-input mb-2']) ?>
        <?= $field->number($form, 'confidence')->addInputAttributes(['class' => 'form-control mb-2']) ?>

        <?= $field->submitButton('Save')->addButtonClass('btn btn-primary') ?>

        <?= Html::form()->close() ?>
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
    </div>
</main>






