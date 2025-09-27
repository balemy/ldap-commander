<?php

declare(strict_types=1);

namespace Balemy\LdapCommander\Asset;

use Yiisoft\Assets\AssetBundle;
use Yiisoft\Bootstrap5\Assets\BootstrapAsset;

final class MultiValueFormFieldAsset extends AssetBundle
{
    public ?string $basePath = '@assets';
    public ?string $baseUrl = '@assetsUrl';
    public ?string $sourcePath = '@resources/assets/js';

    public array $depends = [
        AppAsset::class,
    ];

    public array $js = [
        'multi-value-input.js',
    ];
}
