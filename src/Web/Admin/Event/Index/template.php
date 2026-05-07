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
            <?= Html::a('+ Add', $url->generate('admin:event:create'), [
                'class' => 'btn btn-primary d-flex align-items-center ms-2'
            ]) ?>
        </div>
    </div>
    <div class="grid-view table-responsive">
        <?= GridView::widget()
            ->dataReader($data)
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
                new DataColumn(property: 'title', filter: true),
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
                    },
                    filter: Event::$states
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







