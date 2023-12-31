<?php

declare(strict_types=1);

namespace Balemy\LdapCommander\ViewInjection;

use Balemy\LdapCommander\Modules\Session\Session;
use Yiisoft\Aliases\Aliases;
use Yiisoft\Assets\AssetManager;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Router\CurrentRoute;
use Yiisoft\Yii\View\LayoutParametersInjectionInterface;

final class LayoutViewInjection implements LayoutParametersInjectionInterface
{
    public function __construct(
        private Aliases $aliases,
        private AssetManager $assetManager,
        private UrlGeneratorInterface $urlGenerator,
        private CurrentRoute $currentRoute,
    ) {
    }

    public function getLayoutParameters(): array
    {

        try {
            $session = Session::getCurrentSession();
        } catch (\Exception $e) {
            $session = null;
        }


        return [
            'aliases' => $this->aliases,
            'assetManager' => $this->assetManager,
            'urlGenerator' => $this->urlGenerator,
            'currentRoute' => $this->currentRoute,
            'session' => $session,
        ];
    }
}
