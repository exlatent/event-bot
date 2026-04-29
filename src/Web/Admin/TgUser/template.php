<?php

/** @var \Yiisoft\Data\Paginator\OffsetPaginator $data */

use App\Domain\User\TelegramUser;
use Yiisoft\Yii\DataView\GridView\GridView;
use Yiisoft\Yii\DataView\GridView\Column\DataColumn;
use App\Shared\ApplicationDateTime;

?>

<div class="container">
    <div class="d-flex justify-content-between align-items-start mb-3">
        <h2>Telegram Users</h2>
    </div>
    <div class="table-responsive">
        <?= GridView::widget()
            ->dataReader($data)
            ->urlCreator(function (array $arguments, array $queryParameters): string {
                $url = '';
                if ($queryParameters) {
                    $url .= '?' . http_build_query($queryParameters);
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
                new DataColumn('tg_id'),
                new DataColumn('username'),
                new DataColumn('first_name'),
                new DataColumn('last_name'),
                new DataColumn('language_code'),
                new DataColumn('status'),
                new DataColumn(
                    property: 'createdAt',
                    content: function (TelegramUser $model) {
                        return ApplicationDateTime::toUserTz(ApplicationDateTime::fromDb($model->createdAt));
                    }
                ),
                new DataColumn(
                    property: 'updatedAt',
                    content: function (TelegramUser $model) {
                        return ApplicationDateTime::toUserTz(ApplicationDateTime::fromDb($model->updatedAt));
                    }
                ),
                new DataColumn(
                    property: 'lastActivity',
                    content: function (TelegramUser $model) {
                        return ApplicationDateTime::toUserTz(ApplicationDateTime::fromDb($model->lastActivity));
                    }
                ),
            )
        ?>
    </div>

</div>
