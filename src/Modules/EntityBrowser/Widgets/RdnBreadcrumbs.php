<?php

namespace Balemy\LdapCommander\Modules\EntityBrowser\Widgets;

use Balemy\LdapCommander\LDAP\LdapService;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Widget\Widget;
use Yiisoft\Yii\Bootstrap5\Breadcrumbs;

class RdnBreadcrumbs extends Widget
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
        if ($this->dn !== $this->ldapService->baseDn) {
            $parts = explode(',', substr($this->dn, 0, (strlen($this->ldapService->baseDn) + 1) * -1));
        }

        $parts[] = $this->ldapService->baseDn;

        $links = [];
        foreach ($parts as $i => $part) {
            $full = implode(',', array_slice($parts, $i));

            $links[] = [
                'label' => $part,
                'url' => $this->urlGenerator->generate('entity', [], ['dn' => $full])
            ];
        }

        $links = array_reverse($links);

        return Breadcrumbs::widget()->links($links)->homeLink([])->render();
    }
}