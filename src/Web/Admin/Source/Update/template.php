<?php


/** @var Source $model */
/** @var \App\Web\Admin\Source\Update\Form $form */

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
            ->action('/admin/source/update/'.$model->id)
            ->csrf($csrf)
            ->open()
        ?>

        <?= $field->text($form, 'username')->addInputAttributes(['class' => 'form-control']) ?>
        <?= $field->text($form, 'title')->addInputAttributes(['class' => 'form-control']) ?>
        <?= $field->checkbox($form, 'is_active')->addInputAttributes(['class' => 'form-check']) ?>

        <?= $field->submitButton('Save')->addButtonClass('btn btn-primary') ?>

        <?= Html::form()->close() ?>
    </div>


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


</main>






