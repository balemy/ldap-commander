<?php

namespace Balemy\LdapCommander\Modules\Session;

use Balemy\LdapCommander\LDAP\ConnectionDetails;
use Balemy\LdapCommander\Modules\UserManager\UserManagerConfig;

class ConfiguredSession
{
    public function __construct(
        public ConnectionDetails $connectionDetails,
        public UserManagerConfig $userManager,
    )
    {
    }

    public function getTitle(): string
    {
        return $this->connectionDetails->dsn;
    }

    public function getId(): string
    {
        return md5(sha1($this->connectionDetails->dsn . $this->connectionDetails->adminDn));
    }


    public function login(string $username = '', string $password = ''): bool
    {
        if ($password === $this->connectionDetails->adminPassword) {
            return true;
        }

        if ($this->connectionDetails->adminUserFilter) {
            $session = new Session($this);

            /** @var string[] $user */
            $user = $session->lrConnection->query()
                ->rawFilter(sprintf(
                    $this->connectionDetails->adminUserFilter,
                    $session->lrConnection->query()->escape($username)
                ))->select(['dn'])->first();

            if (!empty($user['dn']) &&
                $session->lrConnection->auth()->attempt($user['dn'], $password)
            ) {
                return true;
            }
        }

        return false;
    }

}
