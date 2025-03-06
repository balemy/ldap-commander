<?php

namespace Balemy\LdapCommander\LDAP\Services;

use Balemy\LdapCommander\Modules\Session\Session;
use LdapRecord\Models\Entry;

final class LdapService
{
    public Session $session;

    public function __construct()
    {
        $this->session = Session::getCurrentSession();
    }


    public function getChildrenCount(string $dn): int
    {
        $query = $this->session->lrConnection->query();
        $query->select(['cn', 'dn'])->setDn($dn)->list();

        $results = $query->paginate();
        return count($results);
    }

    /**
     * @return string[]
     */
    public function getParentDns(string $childrenObjectClass, bool $fallbackToBaseDn = true): array
    {
        $parentDns = [];

        /** @var Entry $entry */
        foreach (Entry::query()->addSelect(['dn'])
                     ->query('(objectClass=' . $childrenObjectClass . ')') as $entry) {

            if (!in_array($entry->getParentDn(), $parentDns)) {
                $parentDns[] = $entry->getParentDn();
            }
        }

        if ($fallbackToBaseDn && empty($parentDns)) {
            $parentDns[] = $this->session->baseDn;
        }

        /** @var string[] $parentDns */
        return $parentDns;
    }

    /**
     * @return string[]
     */
    public function getOrganizationalUnits(): array
    {
        $ous = [];

        /** @var Entry $entry */
        foreach (Entry::query()
                     ->addSelect(['dn', 'cn'])
                     ->query('(objectClass=organizationalUnit)') as $entry) {

            $name = $entry->getName();
            $dn = $entry->getDn();
            if ($name !== null && $dn !== null) {
                $ous[$dn] = $name;
            }
        }

        if (!array_key_exists($this->session->baseDn, $ous)) {
            $baseDnEntry = Entry::query()->find($this->session->baseDn);
            if ($baseDnEntry !== null) {
                /** @var string $orgName */
                $orgName = $baseDnEntry->getFirstAttribute('o');
                $ous = [$this->session->baseDn => $orgName . ' (Base DN)'] + $ous;
            }
        }

        return $ous;
    }


}
