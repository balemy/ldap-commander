<?php

declare(strict_types=1);

namespace Balemy\LdapCommander\Controller;

use Balemy\LdapCommander\Ldap\LdapService;
use Balemy\LdapCommander\Service\WebControllerService;
use Psr\Http\Message\ResponseInterface;
use Yiisoft\Assets\AssetManager;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Session\Flash\FlashInterface;
use Yiisoft\Session\SessionInterface;
use Yiisoft\Yii\View\ViewRenderer;

final class SiteController
{

    public function __construct(public ViewRenderer          $viewRenderer,
                                public LdapService           $ldapService,
                                public WebControllerService  $webService,
                                public UrlGeneratorInterface $urlGenerator,
                                public SessionInterface      $session,
                                public AssetManager          $assetManager,
                                public FlashInterface        $flash
    )
    {
        $this->viewRenderer = $viewRenderer->withControllerName('site')->withLayout('@views/layout/main-nomenu');
    }

    public function index(WebControllerService $webService): ResponseInterface
    {
        return $webService->getRedirectResponse('user-list', []);
    }
}
