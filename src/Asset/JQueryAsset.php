<?php

namespace Balemy\LdapCommander\Asset;

use Yiisoft\Assets\AssetBundle;
use Yiisoft\View\View;

class JQueryAsset extends AssetBundle
{
    public ?string $basePath = '@assets';

    public ?string $baseUrl = '@assetsUrl';

    public ?string $sourcePath = '@npm/jquery/dist';

    public ?int $jsPosition = \Yiisoft\View\WebView::POSITION_HEAD;

    public array $js = [
        'jquery.slim.js',
    ];
}
