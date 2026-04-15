<?php

declare(strict_types=1);

namespace App\Asset;

use Yiisoft\Assets\AssetBundle;

final class BootstrapAsset extends AssetBundle
{
    public ?string $sourcePath = '@assetsSource/vendor/bootstrap';

    public ?string $basePath = '@assets';
    public ?string $baseUrl = '@assetsUrl';

    public array $css = [
        'bootstrap.min.css',
    ];

    public array $js = [
        'bootstrap.min.js',
    ];
}
