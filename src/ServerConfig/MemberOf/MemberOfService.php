<?php

namespace Balemy\LdapCommander\ServerConfig\MemberOf;

use Balemy\LdapCommander\Ldap\LdapService;
use LdapRecord\Models\Entry;
use Yiisoft\Hydrator\Hydrator;

class MemberOfService
{

    public function __construct(private LdapService $ldapService)
    {
    }

    public function populate(MemberOfForm $formModel): void
    {
        $this->getLoadedModules();

        $entry = $this->getConfigEntry();

        (new Hydrator())->hydrate($formModel, [
            'enabled' => true,
            'memberAD' => $entry->getFirstAttribute('olcMemberOfMemberAD'),
            'memberOfAD' => $entry->getFirstAttribute('olcMemberOfMemberOfAD'),
            'groupOC' => $entry->getFirstAttribute('olcMemberOfGroupOC')
        ]);
    }

    public function saveByForm(MemberOfForm $formModel): void
    {
        $entry = $this->getConfigEntry();
        $entry->setFirstAttribute('olcMemberOfGroupOC', $formModel->getPropertyValue('groupOC'));
        $entry->save();
    }

    private function getLoadedModules(): array
    {
        return [];

        /*
        //$modules = [];
        $entries = Entry::query()
            ->setConnection($this->ldapService->configConnection)
            ->in('cn=config')
            ->findManyBy('objectclass', ['olcModuleList']);
        foreach ($entries as $entry) {
            $modules = $entry->getPropertyValue('olcModuleLoad');
            print_r($modules);
            print "<br>";
        }
        die();
        */
    }

    private function addLoadedModule(): void
    {

    }

    private function delLoadedModule(): void
    {

    }

    private function getConfigEntry(): Entry
    {
        /** @var Entry $entry */
        $entry = Entry::query()
            //->setConnection($this->ldapService->configConnection)
            ->in('olcDatabase={2}mdb,cn=config')
            ->findByOrFail('objectclass', 'olcMemberOfConfig');

        $entry->setConnection('config');
        return $entry;
    }
}
