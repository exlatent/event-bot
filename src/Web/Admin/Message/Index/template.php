<?php


/** @var \App\Domain\Telegram\Repository\SourceRepository $source_repository */
/** @var \Yiisoft\Router\UrlGeneratorInterface $url */

/** @var \Yiisoft\View\WebView $this */

use App\Domain\Telegram\Message;
use Yiisoft\Html\Html;
use Yiisoft\Html\Tag\Input\Checkbox;
use Yiisoft\Yii\DataView\GridView\Column\ActionColumn;
use Yiisoft\Yii\DataView\GridView\Column\Base\DataContext;
use Yiisoft\Yii\DataView\GridView\Column\CheckboxColumn;
use Yiisoft\Yii\DataView\GridView\Column\DataColumn;
use Yiisoft\Yii\DataView\GridView\GridView;


?>

<main>
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
            <button type="submit" class="btn btn-sm btn-danger float-right">Remove selected</button>
            <?= Html::form()->close() ?>
        </div>

        <?= GridView::widget()
            ->dataReader($data)
            ->urlCreator(function (array $arguments, array $queryParameters): string {
                $url = '';
                if ($queryParameters) {
                    $url .= '?'.http_build_query($queryParameters);
                }
                return $url;
            })
            ->offsetPaginationConfig([
                'listTag()'           => ['ul'],
                'listAttributes()'    => [['class' => 'pagination']],
                'itemTag()'           => ['li'],
                'itemAttributes()'    => [['class' => 'page-item']],
                'linkAttributes()'    => [['class' => 'page-link']],
                'currentItemClass()'  => ['active'],
                'disabledItemClass()' => ['disabled'],
            ])
            ->tableClass('table table-striped table-hover table-bordered')
            ->columns(
                new DataColumn('id'),
                new DataColumn(
                    property: 'source_id',
                    header: 'Source',
                    content: function (Message $model) use ($source_repository) {
                        return $source_repository->findOne(['id' => $model->source_id])->title ?? 'Unknown';
                    }
                ),
                new DataColumn(
                    property: 'message',
                    content: function (Message $model) {
                        return mb_strlen($model->message) > 100
                            ? mb_substr($model->message, 0, 100).'...'
                            : $model->message;
                    }
                ),
                new DataColumn('date'),
                new DataColumn('createdAt'),
                new DataColumn('event_candidate'),
                new DataColumn('processedAt'),
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

</main>

<?php
$this->registerJsFile('/js/message_list.js', $this::POSITION_END, [
    'defer' => true,
], 'message-list');
?>






