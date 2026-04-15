<?php

/** @var UrlGeneratorInterface $url */

/** @var MessageRepository $message_repository */

use App\Domain\Event\Event;
use App\Domain\Telegram\Repository\MessageRepository;
use Yiisoft\Html\Html;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Yii\DataView\GridView\Column\ActionColumn;
use Yiisoft\Yii\DataView\GridView\Column\Base\DataContext;
use Yiisoft\Yii\DataView\GridView\Column\DataColumn;
use Yiisoft\Yii\DataView\GridView\GridView;

?>

<main>
    <div class="container">
        <div class="d-flex justify-content-between mb-3">
            <h2>Events</h2>
            <?= Html::a('+ Add', $url->generate('admin:event:create'),
                ['class' => 'btn btn-primary d-flex align-items-center']) ?>
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
                new DataColumn('datetime'),
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
</main>






