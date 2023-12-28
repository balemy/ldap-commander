<?php

namespace Balemy\LdapCommander\LDAP;

class ConnectionDetails
{
    final public function __construct(
        public readonly string $dsn,
        public readonly string $baseDn,
        public readonly string $adminDn,
        public readonly string $adminPassword,
        public readonly string $configDn,
        public readonly string $configPassword,
    )
    {
        ;
    }

    /**
     * @param array $config
     * @return static
     */
    public static function fromArray(array $config): static
    {
        return new static(
            dsn: $config['dsn'] ?? 'http://localhost',
            baseDn: $config['baseDn'] ?? '',
            adminDn: $config['adminDn'] ?? '',
            adminPassword: $config['adminPassword'] ?? '',
            configDn: $config['configDn'] ?? '',
            configPassword: $config['configPassword'] ?? '',
        );
    }
}
