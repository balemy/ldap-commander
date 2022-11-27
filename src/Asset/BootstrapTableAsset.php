<?php

declare(strict_types=1);

namespace Balemy\LdapCommander\Asset;

use Yiisoft\Assets\AssetBundle;

final class BootstrapTableAsset extends AssetBundle
{
    public ?string $basePath = '@assets';
    public ?string $baseUrl = '@assetsUrl';
    public ?string $sourcePath = '@npm/bootstrap-table/dist';
    public ?int $cssPosition = \Yiisoft\View\WebView::POSITION_BEGIN;

    public array $css = [
        'bootstrap-table.css',
    ];

    public array $js = [
        'bootstrap-table.js',
    ];
}
