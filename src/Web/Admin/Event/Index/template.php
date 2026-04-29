<?php

/** @var UrlGeneratorInterface $url */
/** @var MessageRepository $message_repository */

/** @var \Yiisoft\Data\Paginator\OffsetPaginator $data */

use App\Domain\Event\Event;
use App\Domain\Telegram\Repository\MessageRepository;
use App\Shared\ApplicationDateTime;
use Yiisoft\Html\Html;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Yii\DataView\GridView\Column\ActionColumn;
use Yiisoft\Yii\DataView\GridView\Column\Base\DataContext;
use Yiisoft\Yii\DataView\GridView\Column\DataColumn;
use Yiisoft\Yii\DataView\GridView\GridView;

?>

<div class="container">
    <div class="d-flex justify-content-between align-items-start mb-3">
        <h2>Events</h2>
        <div class="d-flex">
            <?= Html::button('Filters ↓', [
                'class' => 'btn btn-outline-secondary m-2 mb-0 mt-0',
                'data-bs-toggle' => 'collapse',
                'data-bs-target' => '#filters',
                'aria-expanded' => 'false',
            ]) ?>

            <?= Html::a('+ Add', $url->generate('admin:event:create'), [
                'class' => 'btn btn-primary d-flex align-items-center ms-2'
            ]) ?>
        </div>
    </div>
    <div id="filters" class="collapse mt-2 pb-2">
        <div class="card card-body">
            <?= $this->render('search', [
                'filter' => $filter
            ])?>
        </div>
    </div>
    <div class="table-responsive">
        <?= GridView::widget()
            ->dataReader($data)
            ->urlCreator(function (array $arguments, array $queryParameters): string {
                $url = '';
                if ($queryParameters) {
                    $url .= ' ? ' . http_build_query($queryParameters);
                }
                return $url;
            })
            ->offsetPaginationConfig([
                'listTag()'           => ['ul'],
                'listAttributes()'    => [['class' => 'pagination']],
                'itemTag()'           => ['li'],
                'itemAttributes()'    => [['class' => 'page - item']],
                'linkAttributes()'    => [['class' => 'page - link']],
                'currentItemClass()'  => ['active'],
                'disabledItemClass()' => ['disabled'],
            ])
            ->tableClass('table table-striped table-hover table-bordered')
            ->columns(
                new DataColumn('id'),
                new DataColumn(
                    property: 'message_id',
                    header: 'Message',
                    content: function (Event $model) use ($message_repository, $url) {
                        if ($message = $message_repository->findOne(['id' => $model->message_id])) {
                            return Html::a('link', $url->generate('admin:message:view', ['id' => $message->id]));
                        } else {
                            return 'Unknown';
                        }
                    }
                ),
                new DataColumn('title'),
                new DataColumn(
                    property: 'datetime',
                    content: function (Event $model) {
                        return ApplicationDateTime::toUserTz(ApplicationDateTime::fromDb($model->datetime))->format('d M Y H:i');
                    }
                ),
                new DataColumn('location'),
                new DataColumn('price'),
                new DataColumn(
                    property: 'state',
                    content: function (Event $model) {
                        return Event::$states[$model->state];
                    }
                ),
                new ActionColumn('{view} {update}', null, null, null,
                    function ($action, DataContext $context) use ($url) {
                        return $url->generate("admin:event:{$action}", ['id' => $context->data->id]);
                    }
                )
            )
        ?>
    </div>

</div>







