<?php

namespace App\Ldap;

use LdapRecord\Models\Entry;

class User
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

    public function getUsername(): string
    {
        return $this->getEntryValue('uid') ?? '';
    }

    public function getFirstName(): ?string
    {
        return $this->getEntryValue('givenName') ?? null;
    }

    public function getLastName(): ?string
    {
        return $this->getEntryValue('sn') ?? null;
    }

    public function getMail(): ?string
    {
        return $this->getEntryValue('mail') ?? null;
    }

    public function getDn(): string
    {
        return $this->entry->getDn() ?? '';
    }

    public function getDisplayName(): string
    {
        $displayName = '';
        $firstName = $this->getFirstName();
        $lastName = $this->getFirstName();

        if ($firstName !== null) {
            $displayName = $firstName;
        }
        if ($lastName !== null) {
            if ($displayName !== '') {
                $displayName .= '';
            }
            $displayName .= $lastName;
        }
        return $displayName;
    }

    /**
     * @return User[]
     */
    public static function getAll(): array
    {
        $users = [];
        /** @var Entry $entry */
        foreach (\LdapRecord\Models\OpenLDAP\User::all() as $entry) {
            $users[] = new User($entry);
        }
        return $users;
    }

}
