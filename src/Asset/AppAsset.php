<?php

declare(strict_types=1);

namespace Balemy\LdapCommander\Asset;

use Yiisoft\Assets\AssetBundle;
use Yiisoft\Bootstrap5\Assets\BootstrapAsset;

final class AppAsset extends AssetBundle
{
    public ?string $basePath = '@assets';
    public ?string $baseUrl = '@assetsUrl';
    public ?string $sourcePath = '@resources/assets/css';

    public array $depends = [
        BootstrapAsset::class,
        BootstrapTableAsset::class,
        Select2Asset::class,
        JQueryAsset::class,
        FontAwesomeAsset::class
    ];

    public array $css = [
        'site.css',
    ];
}
