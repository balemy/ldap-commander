<?php

declare(strict_types=1);

namespace Balemy\LdapCommander\Modules\SchemaBrowser;

use Balemy\LdapCommander\LDAP\LdapService;
use Balemy\LdapCommander\Service\WebControllerService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Yiisoft\Assets\AssetManager;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Session\Flash\FlashInterface;
use Yiisoft\Session\SessionInterface;
use Yiisoft\Yii\View\ViewRenderer;

final class SchemaController
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
        $this->viewRenderer = $viewRenderer->withViewPath(__DIR__ . '/Views/');
    }

    public function index(WebControllerService $webService): ResponseInterface
    {
        return $this->viewRenderer->render('index', [
            'objectClasses' => $this->ldapService->getSchema()->objectClasses,
            'urlGenerator' => $this->urlGenerator
        ]);
    }

    public function displayObjectClass(WebControllerService $webService, ServerRequestInterface $request): ResponseInterface
    {
        $oid = '';
        if (isset($request->getQueryParams()['oid']) && is_string($request->getQueryParams()['oid'])) {
            $oid = (string)$request->getQueryParams()['oid'];
        }

        $objectClass = $this->ldapService->getSchema()->getObjectClassByOid($oid);
        if ($objectClass === null) {
            return $webService->getNotFoundResponse();
        }

        return $this->viewRenderer->render('objectclass', [
            'objectClass' => $objectClass,
            'urlGenerator' => $this->urlGenerator
        ]);
    }

    public function displayAttribute(WebControllerService $webService, ServerRequestInterface $request): ResponseInterface
    {
        $oid = '';
        if (isset($request->getQueryParams()['oid']) && is_string($request->getQueryParams()['oid'])) {
            $oid = (string)$request->getQueryParams()['oid'];
        }

        $attribute = $this->ldapService->getSchema()->getAttributeTypeByOid($oid);
        if ($attribute === null) {
            return $webService->getNotFoundResponse();
        }

        return $this->viewRenderer->render('attribute', [
            'attribute' => $attribute,
            'objectClasses' => $this->ldapService->getSchema()->getObjectClassesByAttributeType($attribute),
            'urlGenerator' => $this->urlGenerator,
        ]);
    }
}
