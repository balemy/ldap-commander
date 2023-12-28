<?php

namespace Balemy\LdapCommander\Modules\Session;

use Balemy\LdapCommander\LDAP\ConnectionDetails;
use Balemy\LdapCommander\Modules\UserManager\UserManagerConfig;

class Session
{
    public function __construct(
        public ConnectionDetails $connectionDetails,
        public UserManagerConfig $userManager,
    )
    {
        ;
    }

    public function getTitle(): string
    {
        return $this->connectionDetails->dsn;
    }

    public function login(string $username = '', string $password = ''): bool
    {

        //TODO: Implement me

        if ($password === $this->connectionDetails->adminPassword) {
            return true;
        }

        return false;
    }

    public function getId(): string
    {
        return md5(sha1($this->connectionDetails->dsn . $this->connectionDetails->adminDn));
    }
}
