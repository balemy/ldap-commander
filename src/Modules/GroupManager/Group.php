<?php

namespace Balemy\LdapCommander\Modules\GroupManager;

use LdapRecord\Models\Entry;
use LdapRecord\Models\OpenLDAP\Group as LrGroup;

final class Group
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

    public static function getOne(string $dn): ?Group
    {
        /** @var Entry|null $entry */
        $entry = Entry::query()->find($dn);
        if ($entry !== null) {
            return new Group($entry);
        }

        return null;
    }


    public function addMember(?string $addDn): bool
    {
        if ($addDn === null) {
            return false;
        }

        $this->entry->addAttributeValue('uniquemember', $addDn);
        $this->entry->save();

        return true;
    }

    public function removeMember(?string $delDn): bool
    {
        if ($delDn === null) {
            return false;
        }

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

    public function __toString(): string
    {
        return $this->getDn();
    }

    public function isNewRecord(): bool
    {
        return empty($this->entry->getDn());
    }

}
