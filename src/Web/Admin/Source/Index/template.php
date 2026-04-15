<?php


/** @var Source[] $sources */

/** @var \Yiisoft\Router\UrlGeneratorInterface $url */

use App\Domain\Telegram\Source;
use Yiisoft\Yii\DataView\GridView\Column\ActionColumn;
use Yiisoft\Yii\DataView\GridView\Column\Base\DataContext;
use Yiisoft\Yii\DataView\GridView\Column\DataColumn;
use Yiisoft\Yii\DataView\GridView\GridView;
use Yiisoft\Data\Paginator\PaginatorInterface;
use Yiisoft\Data\Reader\Iterable\IterableDataReader;
use Yiisoft\Data\Paginator\OffsetPaginator;


?>

<main>
    <div class="container">
        <div class="d-flex justify-content-between mb-3">
            <h2>Sources</h2>
            <?= \Yiisoft\Html\Html::a('+ Add', $url->generate('admin:source:create'),
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
                new DataColumn('username'),
                new DataColumn('title'),
                new DataColumn('createdAt'),
                new DataColumn('updatedAt'),
                new DataColumn('is_active'),
                new ActionColumn('{update}', null, null, null,
                    function ($action, DataContext $context) use ($url) {
                        return $url->generate('admin:source:update', ['id' => $context->data->id]);
                    }
                )
            )
        ?>

    </div>

</main>






