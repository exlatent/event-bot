<?php

/** @var SourceRepository $source_repository */
/** @var UrlGeneratorInterface $url */
/** @var WebView $this */
/** @var DataReader $data */

use App\Domain\Telegram\Message;
use App\Domain\Telegram\Repository\SourceRepository;
use App\Shared\ApplicationDateTime;
use App\Web\Admin\Message\Index\DataReader;
use Yiisoft\Html\Html;
use Yiisoft\Html\Tag\Input\Checkbox;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\View\WebView;
use Yiisoft\Yii\DataView\GridView\Column\ActionColumn;
use Yiisoft\Yii\DataView\GridView\Column\Base\DataContext;
use Yiisoft\Yii\DataView\GridView\Column\CheckboxColumn;
use Yiisoft\Yii\DataView\GridView\Column\DataColumn;
use Yiisoft\Yii\DataView\GridView\GridView;


?>
<div class="container">
    <div class="d-flex justify-content-between mb-3">
        <h2>Messages</h2>
        <?=
        Html::form()
            ->post()
            ->action('/admin/message')
            ->csrf($csrf)
            ->open()
        ?>
        <?= Html::hiddenInput('selected') ?>
        <button type="submit" class="btn btn-danger float-right">Remove selected</button>
        <?= Html::form()->close() ?>
    </div>
    <div class="grid-view table-responsive">
        <?= GridView::widget()
            ->dataReader($data)
            ->tableClass('table table-striped table-hover table-bordered')
            ->columns(
                new DataColumn('id'),
                new DataColumn(
                    property: 'source_id',
                    header: 'Source',
                    content: function (Message $model) use ($source_repository) {
                        return $source_repository->findOne(['id' => $model->source_id])->title ?? 'Unknown';
                    },
                    filter: $source_repository->getList()
                ),
                new DataColumn(
                    property: 'message',
                    content: function (Message $model) {
                        return mb_strlen($model->message) > 100
                            ? mb_substr($model->message, 0, 100) . '...'
                            : $model->message;
                    },
                    filter: true
                ),
                new DataColumn(
                    property: 'date',
                    content: function (Message $model) {
                        return ApplicationDateTime::toUserTz(ApplicationDateTime::fromDb($model->date));
                    }
                ),
                new DataColumn(
                    property: 'createdAt',
                    content: function (Message $model) {
                        return ApplicationDateTime::toUserTz(ApplicationDateTime::fromDb($model->createdAt));
                    }
                ),
                new DataColumn(
                    property: 'event_candidate',
                    filter: [0 => 'No', 1 => 'Yes']),
                new DataColumn(
                    property: 'processedAt',
                    content: function (Message $model) {
                        if ($model->processedAt) {
                            return ApplicationDateTime::toUserTz(ApplicationDateTime::fromDb($model->processedAt));
                        }
                    }
                ),

                new ActionColumn('{view} {update}', null, null, null,
                    function ($action, DataContext $context) use ($url) {
                        return $url->generate("admin:message:{$action}", ['id' => $context->data->id]);
                    }
                ),
                new CheckboxColumn(
                    name: 'selection',
                    multiple: true,
                    content: function (Checkbox $input, DataContext $context) {
                        return "<input type=\"checkbox\" name=\"selection\" value=\"{$context->data->id}\">";
                    }
                ),
            )
        ?>
    </div>

</div>

<?php
$this->registerJsFile('/js/message_list.js', $this::POSITION_END, [
    'defer' => true,
], 'message-list');
?>






