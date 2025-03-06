<?php

namespace Balemy\LdapCommander\LDAP;

final class ConnectionDetails
{
    final public function __construct(
        public readonly string $dsn,
        public readonly string $baseDn,
        public readonly string $adminDn,
        #[\SensitiveParameter]
        public readonly string $adminPassword,
        public readonly string $adminUserFilter,
        public readonly string $configDn,
        #[\SensitiveParameter]
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
            dsn: is_string($config['dsn']) ? $config['dsn'] : 'http://localhost',
            baseDn: is_string($config['baseDn']) ? $config['baseDn'] : '',
            adminDn: is_string($config['adminDn']) ? $config['adminDn'] : '',
            adminPassword: is_string($config['adminPassword']) ? $config['adminPassword'] : '',
            adminUserFilter: isset($config['adminUserFilter']) && is_string($config['adminUserFilter']) ? $config['adminUserFilter'] : '',
            configDn: is_string($config['configDn']) ? $config['configDn'] : '',
            configPassword: is_string($config['configPassword']) ? $config['configPassword'] : '',
        );
    }
}
