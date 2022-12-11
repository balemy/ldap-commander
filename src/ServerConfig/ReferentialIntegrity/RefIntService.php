<?php

namespace Balemy\LdapCommander\ServerConfig\ReferentialIntegrity;

use Balemy\LdapCommander\Ldap\LdapService;
use Balemy\LdapCommander\Ldap\LoginForm;
use LdapRecord\Models\Entry;
use Yiisoft\Session\SessionInterface;

class RefIntService
{

    public function __construct(private LdapService $ldapService)
    {
    }

    public function populate(RefForm $formModel): void
    {
        /*
        $entry = Entry::query()
            ->setConnection($this->ldapService->configConnection)
            ->findOrFail('cn=module{0},cn=config');

        print "<pre>";
        print_r($entry);
        */
    }

    public function saveByForm(RefForm $formModel): void
    {
    }
}
