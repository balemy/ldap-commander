<?php

declare(strict_types=1);

namespace App\Asset;

use Yiisoft\Assets\AssetBundle;

final class Select2Bootstrap5Asset extends AssetBundle
{
    public ?string $basePath = '@assets';
    public ?string $baseUrl = '@assetsUrl';
    public ?string $sourcePath = '@npm/select2-bootstrap-5-theme/dist';
    public ?int $cssPosition = \Yiisoft\View\WebView::POSITION_BEGIN;

    public array $css = [
        'select2-bootstrap-5-theme.css',
    ];
}
