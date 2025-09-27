<?php

namespace Balemy\LdapCommander\Modules\EntityBrowser\Widgets;

use Balemy\LdapCommander\LDAP\Services\LdapService;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Widget\Widget;
use Yiisoft\Bootstrap5\BreadcrumbLink;
use Yiisoft\Bootstrap5\Breadcrumbs;

final class RdnBreadcrumbs extends Widget
{
    /**
     * @var string
     */
    public $dn = '';

    /**
     * {@inheritDoc}
     */
    public function __construct(public LdapService $ldapService, public UrlGeneratorInterface $urlGenerator)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function render(): string
    {
        $parts = [];
        if ($this->dn !== $this->ldapService->session->baseDn) {
            $parts = explode(',', substr($this->dn, 0, (strlen($this->ldapService->session->baseDn) + 1) * -1));
        }

        $parts[] = $this->ldapService->session->baseDn;

        $links = [];
        foreach ($parts as $i => $part) {
            $full = implode(',', array_slice($parts, $i));
            $links[] = BreadcrumbLink::to($part, $this->urlGenerator->generate('entity', [], ['dn' => $full]));
        }

        $links = array_reverse($links);

        return Breadcrumbs::widget()->links(...$links)/*->homeLink([])*/->render();
    }
}
