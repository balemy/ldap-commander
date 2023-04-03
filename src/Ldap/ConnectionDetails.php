<?php

namespace Balemy\LdapCommander\Ldap;

use Yiisoft\Session\SessionInterface;

class ConnectionDetails
{
    public string $title = '';
    public string $dsn = '';
    public string $baseDn = '';
    public string $adminDn = '';
    public string $adminPassword = '';

    const MAX_ENV_CONNECTIONS = 5;

    /**
     * @var string[]
     */
    const ENV_CONFIG_MAP = [
        'title' => 'LDAPCOM_CONN%d_TITLE',
        'dsn' => 'LDAPCOM_CONN%d_DSN',
        'baseDn' => 'LDAPCOM_CONN%d_BASE_DN',
        'adminDn' => 'LDAPCOM_CONN%d_ADMIN_DN',
        'adminPassword' => 'LDAPCOM_CONN%d_ADMIN_PASSWORD',
    ];

    /**
     * @var ConnectionDetails[]|null
     */
    private static $_connections = null;

    public function __construct(
        string $title = '',
        string $dsn = '',
        string $baseDn = '',
        string $adminDn = '',
        string $adminPassword = '',
    )
    {
        if (!empty($title)) {
            $this->title = $title;
        }

        if (!empty($dsn)) {
            $this->dsn = $dsn;
        }

        if (!empty($baseDn)) {
            $this->baseDn = $baseDn;
        }
        if (!empty($adminDn)) {
            $this->adminDn = $adminDn;
        }
        if (!empty($adminPassword)) {
            $this->adminPassword = $adminPassword;
        }
    }

    /**
     * @return ConnectionDetails[]
     */
    public static function getAll(): array
    {
        if (static::$_connections !== null) {
            return static::$_connections;
        }

        $connectionDetails = [];

        for ($i = 1; $i <= static::MAX_ENV_CONNECTIONS; $i++) {
            if (static::getEnvVar('title', $i) !== null || static::getEnvVar('dsn', $i) !== null) {

                $connectionDetails[] = new ConnectionDetails(
                    static::getEnvVar('title', $i) ?? static::getEnvVar('dsn', $i) ?? '',
                    static::getEnvVar('dsn', $i) ?? static::getEnvVar('title', $i) ?? '',
                    static::getEnvVar('baseDn', $i) ?? '',
                    static::getEnvVar('adminDn', $i) ?? '',
                    static::getEnvVar('adminPassword', $i) ?? '',
                );
            }
        }

        if (isset($_ENV['LDAPCOM_ALLOW_CUSTOM_CONNECT']) && $_ENV['LDAPCOM_ALLOW_CUSTOM_CONNECT'] == "1") {
            $connectionDetails[99] = new ConnectionDetails('Custom');
        }

        static::$_connections = $connectionDetails;

        return static::$_connections;
    }


    private static function getEnvVar(string $name, int $i): ?string
    {
        if (!isset(static::ENV_CONFIG_MAP[$name])) {
            return null;
        }

        $name = sprintf(static::ENV_CONFIG_MAP[$name], $i);

        if (isset($_ENV[$name]) && is_string($_ENV[$name])) {
            return $_ENV[$name];
        }

        return null;
    }

}
