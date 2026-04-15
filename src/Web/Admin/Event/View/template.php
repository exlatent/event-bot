<?php

/** @var Event $model */

/** @var \Yiisoft\Router\UrlGeneratorInterface $url */

use App\Domain\Event\Event;
use Yiisoft\Yii\DataView\DetailView\DataField;
use Yiisoft\Yii\DataView\DetailView\DetailView;
use Yiisoft\Html\Html;


?>

<main>
    <div class="container">
        <div class="d-flex justify-content-between mb-3">
            <h2><?= $model->title ?></h2>
            <?= Html::a('Update', $url->generate('admin:event:update', ['id' => $model->id]),
                ['class' => 'btn btn-primary d-flex align-items-center']) ?>
        </div>

        <?= DetailView::widget()
            ->data($model)
            ->listTag('table')
            ->listAttributes(['class' => 'table table-striped table-bordered'])
            ->labelTag('th')
            ->valueTag('td')
            ->fieldTag('tr')
            ->fields(
                new DataField('id'),
                new DataField(
                    property: 'message_id',
                    label: 'Message'
                ),
                new DataField('title'),
                new DataField('datetime'),
                new DataField('location'),
                new DataField('price'),
                new DataField('createdAt'),
                new DataField('updatedAt'),
                new DataField('state'),
                new DataField('duplicate_of_id'),
                new DataField('lastCheckedAt')
            )
        ?>

    </div>

</main>






