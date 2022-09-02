<?php

declare(strict_types=1);

namespace App\Asset;

use Yiisoft\Assets\AssetBundle;

final class Select2Asset extends AssetBundle
{
    public ?string $basePath = '@assets';

    public ?string $baseUrl = '@assetsUrl';

    public ?string $sourcePath = '@npm/select2/dist';

    public array $depends = [
        Select2Bootstrap5Asset::class,
    ];

    public array $css = [
        'css/select2.css',
    ];

    public array $js = [
        'js/select2.full.js',
    ];
}
