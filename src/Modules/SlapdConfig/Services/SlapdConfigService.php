<?php

namespace Balemy\LdapCommander\Modules\SlapdConfig\Services;

use Balemy\LdapCommander\LDAP\Helper\DSN;
use Balemy\LdapCommander\LDAP\Schema\Schema;
use Balemy\LdapCommander\Modules\Session\Session;
use LdapRecord\Connection;
use LdapRecord\Container;
use LdapRecord\Models\Entry;

final class SlapdConfigService
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

    public function getDatabaseConfigDn(): string
    {

        return $this->getDatabaseConfigEntry()?->getDn() ?? '';
    }

    public function getLoadedModules(): array
    {
        $modules = [];
        /** @var Entry[] $entries */
        $entries = Entry::query()
            // ->setConnection($configService->lrConnection)
            ->where('objectclass', '=', 'olcModuleList')
            ->get();

        foreach ($entries as $entry) {
            $modules = array_merge($modules, (array)$entry->getAttribute('olcModuleLoad'));
        }

        $modules = array_map(function (string $e) {
            return preg_replace("/^\{\d+\}/", '', $e);
        }, $modules);

        return $modules;
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
