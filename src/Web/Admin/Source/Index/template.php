<?php

/** @var Source[] $sources */
/** @var UrlGeneratorInterface $url */

/** @var DataReader $data */

use App\Domain\Telegram\Source;
use App\Shared\ApplicationDateTime;
use App\Web\Admin\Source\Index\DataReader;
use Yiisoft\Html\Html;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Yii\DataView\GridView\Column\ActionColumn;
use Yiisoft\Yii\DataView\GridView\Column\Base\DataContext;
use Yiisoft\Yii\DataView\GridView\Column\DataColumn;
use Yiisoft\Yii\DataView\GridView\GridView;


?>

<div class="container">
    <div class="d-flex justify-content-between align-items-start mb-3">
        <h2>Sources</h2>
        <?= Html::a('+ Add', $url->generate('admin:source:create'),
            ['class' => 'btn btn-primary d-flex align-items-center']) ?>
    </div>
    <div class="grid-view table-responsive">
        <?= GridView::widget()
            ->dataReader($data)
            ->tableClass('table table-striped table-hover table-bordered')
            ->columns(
                new DataColumn('id'),
                new DataColumn(
                    property: 'username',
                    filter: true
                ),
                new DataColumn(
                    property: 'title',
                    filter: true
                ),
                new DataColumn(
                    property: 'createdAt',
                    content: function (Source $model) {
                        return ApplicationDateTime::toUserTz(ApplicationDateTime::fromDb($model->createdAt));
                    }
                ),
                new DataColumn(
                    property: 'updatedAt',
                    content: function (Source $model) {
                        return ApplicationDateTime::toUserTz(ApplicationDateTime::fromDb($model->updatedAt));
                    }
                ),
                new DataColumn(
                    property: 'is_active',
                    filter: [0 => 'False', 1 => 'True']
                ),
                new ActionColumn('{update}', null, null, null,
                    function ($action, DataContext $context) use ($url) {
                        return $url->generate('admin:source:update', ['id' => $context->data->id]);
                    }
                )
            )
        ?>
    </div>
</div>






