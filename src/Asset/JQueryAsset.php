<?php

namespace App\Asset;

use Yiisoft\Assets\AssetBundle;

class JQueryAsset extends AssetBundle
{
    public ?string $basePath = '@assets';

    public ?string $baseUrl = '@assetsUrl';

    public ?string $sourcePath = '@npm/jquery/dist';

    public array $js = [
        'jquery.slim.js',
    ];
}
