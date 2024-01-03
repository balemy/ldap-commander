<?php

namespace Balemy\LdapCommander\Modules\SlapdConfig\Services;

use Balemy\LdapCommander\LDAP\Helper\DSN;
use Balemy\LdapCommander\Modules\Session\Session;
use LdapRecord\Connection;
use LdapRecord\Container;
use LdapRecord\Models\Entry;

class SlapdConfigService
{
    public Connection $lrConnection;

    public function __construct()
    {

        $connectionDetails = $this->getSession()->connectionDetails;

        $dsn = new DSN($connectionDetails->dsn);

        $config = [
            'hosts' => [$dsn->getHost()],
            'port' => $dsn->getPort(),
            'use_ssl' => $dsn->getIsSSL(),
            'username' => $connectionDetails->configDn,
            'password' => $connectionDetails->configPassword,
            'base_dn' => 'cn=config'
        ];
        $this->lrConnection = new Connection($config);
        $this->lrConnection->connect();


        Container::addConnection($this->lrConnection, 'config');
        Container::setDefaultConnection('config');

    }

    public function getDatabaseConfigEntry(): ?Entry
    {
        /** @var Entry $lrEntry */
        $lrEntry = Entry::query()
            // ->setConnection($configService->lrConnection)
            ->where('objectclass', '=', 'olcDatabaseConfig')
            ->where('olcSuffix', '=', $this->getSession()->connectionDetails->baseDn)
            ->first();

        return $lrEntry;
    }

    public function getSession(): Session
    {
        return Session::getCurrentSession();
    }
}
