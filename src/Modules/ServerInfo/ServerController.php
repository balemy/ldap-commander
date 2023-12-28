<?php

declare(strict_types=1);

namespace Balemy\LdapCommander\Modules\ServerInfo;

use Balemy\LdapCommander\Ldap\LdapService;
use Balemy\LdapCommander\Service\WebControllerService;
use Psr\Http\Message\ResponseInterface;
use Yiisoft\Assets\AssetManager;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Session\Flash\FlashInterface;
use Yiisoft\Session\SessionInterface;
use Yiisoft\Yii\View\ViewRenderer;

final class ServerController
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
        $query = $this->ldapService->connection->query();

        $infoAttrs = [
            'namingContexts',
            'subschemaSubentry',
            'altServer',
            'supportedExtension',
            'supportedControl',
            'supportedSASLMechanisms',
            'supportedLDAPVersion',
            'currentTime',
            'dsServiceName',
            'defaultNamingContext',
            'schemaNamingContext',
            'configurationNamingContext',
            'rootDomainNamingContext',
            'supportedLDAPPolicies',
            'highestCommittedUSN',
            'dnsHostName',
            'ldapServiceName',
            'serverName',
            'supportedCapabilities',
            'changeLog',
            'tlsAvailableCipherSuites',
            'tlsImplementationVersion',
            'supportedSASLMechanisms',
            'dsaVersion',
            'myAccessPoint',
            'dseType',
            '+',
            '*'];

        $query->read()
            ->setBaseDn('')
            ->addSelect($infoAttrs)
            ->rawFilter('(objectClass=*)');

        /** @var array[] $result */
        $result = $query->get();

        $res = $result[0];


        foreach ($res as $i => $values) {
            if (!is_array($values)) {
                unset($res[$i]);
                continue;
            }

            /** @var string $ii */
            foreach ($values as $ii => $_) {
                if ($ii === 'count') {
                    /**
                     * @psalm-suppress MixedArrayAccess
                     */
                    unset($res[$i][$ii]);
                }
            }
        }

        return $this->viewRenderer->render('index', [
            'objectClasses' => $this->ldapService->getSchema()->objectClasses,
            'urlGenerator' => $this->urlGenerator,
            'results' => $res
        ]);
    }
}
