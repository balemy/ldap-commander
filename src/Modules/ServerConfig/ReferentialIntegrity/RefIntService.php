<?php

namespace Balemy\LdapCommander\Modules\ServerConfig\ReferentialIntegrity;

use Balemy\LdapCommander\Ldap\LdapService;

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
