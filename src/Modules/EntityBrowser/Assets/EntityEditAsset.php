<?php

declare(strict_types=1);

namespace Balemy\LdapCommander\Modules\EntityBrowser\Assets;

use Balemy\LdapCommander\Asset\AppAsset;
use Balemy\LdapCommander\Asset\MultiValueFormFieldAsset;
use Yiisoft\Assets\AssetBundle;

final class EntityEditAsset extends AssetBundle
{
    public ?string $basePath = '@assets';
    public ?string $baseUrl = '@assetsUrl';
    public ?string $sourcePath = '@resources/assets/js';

    public array $depends = [
        AppAsset::class,
        MultiValueFormFieldAsset::class,
    ];

    public array $js = [
        'entity-edit.js',
    ];
}
