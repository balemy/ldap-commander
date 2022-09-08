<?php

namespace App\Ldap;

use LdapRecord\Models\Entry;

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
        return $this->getEntryValue('ou') ?? null;
    }


    public function getDn(): string
    {
        return $this->entry->getDn() ?? '';
    }


    /**
     * @return Group[]
     */
    public static function getAll(): array
    {
        $groups = [];
        /** @var Entry $entry */
        foreach (\LdapRecord\Models\OpenLDAP\Group::all() as $entry) {
            $groups[] = new Group($entry);
        }
        return $groups;
    }

}
