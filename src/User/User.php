<?php

namespace Balemy\LdapCommander\User;

use Balemy\LdapCommander\Group\Group;

/** @psalm-suppress PropertyNotSetInConstructor */
class User extends \LdapRecord\Models\OpenLDAP\User
{
    public function getDisplayName(): string
    {
        return (string)$this->getFirstAttribute('cn');
    }

    /**
     * @return Group[]
     */
    public function getGroups(): array
    {
        $groups = [];
        if (!empty($this->getAttributeValue('memberof')) && is_array($this->getAttributeValue('memberof'))) {
            /** @var string[] $memberOf */
            $memberOf = $this->getAttributeValue('memberof');
            foreach ($memberOf as $groupDn) {
                $group = Group::getOne($groupDn);
                if ($group !== null) {
                    $groups[] = $group;
                }
            }
        }
        return $groups;
    }
}
