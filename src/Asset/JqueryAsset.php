<?php

declare(strict_types=1);

namespace App\Asset;

use Yiisoft\Assets\AssetBundle;

final class JqueryAsset extends AssetBundle
{
    public ?string $sourcePath = '@assetsSource/vendor/jquery';

    public ?string $basePath = '@assets';
    public ?string $baseUrl = '@assetsUrl';

    public array $js = [
        'jquery.min.js',
    ];
}
