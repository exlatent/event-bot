<?php


/** @var \App\Domain\Telegram\Repository\SourceRepository $source_repository */
/** @var Message $model */
/** @var \Yiisoft\Router\UrlGeneratorInterface $url */
/** @var Source $source */

use App\Domain\Telegram\Message;
use App\Domain\Telegram\Source;
use App\Shared\ApplicationDateTime;
use Yiisoft\Yii\DataView\DetailView\DataField;
use Yiisoft\Yii\DataView\DetailView\DetailView;
use Yiisoft\Html\Html;


?>

<main>
    <div class="container">
        <div class="d-flex justify-content-between mb-3">
            <h2>Message ID <?= $model->id ?></h2>
            <?= Html::a('Update', $url->generate('admin:message:update', ['id' => $model->id]),
                ['class' => 'btn btn-primary d-flex align-items-center'])?>
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
                    property: 'source_id',
                    label: 'Source',
                    value: $source->title ?? 'Unknown'
                ),
                new DataField('tg_id'),
                new DataField('source_tg_id'),
                new DataField('message'),
                new DataField(
                    property: 'date',
                    value: ApplicationDateTime::toUserTz(ApplicationDateTime::fromDb($model->date))
                ),
                new DataField(
                    property: 'createdAt',
                    value: ApplicationDateTime::toUserTz(ApplicationDateTime::fromDb($model->createdAt))
                ),
                new DataField(
                    property: 'analyzedAt',
                    value: $model->analyzedAt
                        ? ApplicationDateTime::toUserTz(ApplicationDateTime::fromDb($model->analyzedAt))
                        : ''
                ),
                new DataField('event_candidate'),
                new DataField('spam'),
                new DataField('off_topic'),
                new DataField('confidence'),
                new DataField(
                    property: 'processedAt',
                    value: $model->processedAt
                        ? ApplicationDateTime::toUserTz(ApplicationDateTime::fromDb($model->processedAt))
                        : ''
                ),
            )
        ?>

    </div>

</main>






