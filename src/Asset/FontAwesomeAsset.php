<?php

declare(strict_types=1);

namespace Balemy\LdapCommander\Asset;

use Yiisoft\Assets\AssetBundle;

final class FontAwesomeAsset extends AssetBundle
{
    public ?string $basePath = '@assets';

    public ?string $baseUrl = '@assetsUrl';

    public ?string $sourcePath = '@npm/font-awesome';

    public array $depends = [
        Select2Bootstrap5Asset::class,
    ];

    public array $css = [
        'css/all.css',
    ];

    public array $js = [
        'js/all.js',
    ];
}
