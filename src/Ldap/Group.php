<?php

namespace App\Ldap;

use LdapRecord\Models\ActiveDirectory\Computer;
use LdapRecord\Models\ActiveDirectory\Contact;
use LdapRecord\Models\ActiveDirectory\User;
use LdapRecord\Models\Entry;
use LdapRecord\Models\OpenLDAP\Group as LrGroup;
use LdapRecord\Models\OpenLDAP\User as LrUser;

class Group
{

    private $entry;


    public function __construct(Entry $entry)
    {
        $this->entry = $entry;
    }

    private function getEntryValue(string $name): ?string
    {
        /**
         * @psalm-suppress MixedAssignment
         */
        $res = $this->entry->getAttributeValue($name);
        if (is_array($res)) {
            if (isset($res[0]) && is_string($res[0])) {
                return $res[0];
            }
        }

        return null;
    }


    public function getId(): ?int
    {
        $id = $this->getEntryValue('uidNumber');
        if ($id !== null) {
            return intval($id);
        }

        return null;
    }

    public function getTitle(): ?string
    {
        return $this->getEntryValue('cn') ?? null;
    }


    public function getDn(): string
    {
        return $this->entry->getDn() ?? '';
    }


    public function getUserDns(): array
    {
        $arr = $this->entry->getAttributeValue('uniquemember');
        if (!is_array($arr)) {
            return [];
        }

        return $arr;
    }


    /**
     * @return Group[]
     */
    public static function getAll(): array
    {
        $groups = [];
        /** @var Entry $entry */
        foreach (LrGroup::all() as $entry) {
            $groups[] = new Group($entry);
        }
        return $groups;
    }

    public function addMember(string $addDn): bool
    {
        $this->entry->addAttributeValue('uniquemember', $addDn);
        $this->entry->save();

        return true;
    }

    public function removeMember(string $delDn): bool
    {
        $attribute = $this->entry->getAttributeValue('uniquemember');
        assert(is_array($attribute));

        /** @var array<string, string> $array */
        foreach ($attribute as $key => $val) {
            assert(is_string($val));
            if ($val === $delDn) {
                unset($attribute[$key]);
                $this->entry->setAttribute('uniquemember', $attribute);
                $this->entry->save();
                return true;
            }
        }
        return false;
    }

    public function getDescription(): string
    {
        return $this->getEntryValue('description') ?? '';
    }

    public function update(GroupForm $formModel): bool
    {
        $this->entry->setFirstAttribute('cn', $formModel->getTitle());
        $this->entry->setFirstAttribute('description', $formModel->getDescription());
        $this->entry->save();
        return true;
    }

}
