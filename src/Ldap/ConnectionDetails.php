<?php

namespace App\Ldap;

use Yiisoft\Session\SessionInterface;

class ConnectionDetails
{
    /**
     * @var array{fragment?: string, host?: string, pass?: string, path?: string, port?: int, query?: string, scheme?: string, user?: string}|false
     */
    private $parseUrl = [];

    /**
     * @var string
     */
    public string $dsn = '';
    public string $baseDn = '';
    public string $adminDn = '';
    public string $adminPassword = '';

    /**
     * @var string[]
     */
    const ENV_CONFIG_MAP = [
        'dsn' => 'CMDA_DSN',
        'adminDn' => 'CMDA_ADMIN_DN',
        'adminPassword' => 'CMDA_ADMIN_PASSWORD',
        'baseDn' => 'CMDA_BASE_DN',
    ];


    public function __construct(
        string $dsn = '',
        string $baseDn = '',
        string $adminDn = '',
        string $adminPassword = '',
    )
    {
        $this->loadDefaults();

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

        $this->parseUrl = parse_url($this->dsn);
    }

    private function loadDefaults(): void
    {
        foreach (static::ENV_CONFIG_MAP as $attribute => $envVar) {
            assert(is_string($envVar));
            if (isset($_SERVER[$envVar]) && is_string($_SERVER[$envVar])) {
                assert(is_string($attribute));
                $this->$attribute = $_SERVER[$envVar];
            }
        }
    }


    public static function createFromSession(SessionInterface $session): ConnectionDetails
    {
        /** @var ConnectionDetails|null $connectionDetails */
        $connectionDetails = $session->get('ConnectionDetails');
        if ($connectionDetails instanceof ConnectionDetails) {
            return $connectionDetails;
        }

        return new ConnectionDetails();
    }


    public static function removeFromSession(SessionInterface $session): void
    {
        $session->remove('ConnectionDetails');

        /*
        $c = static::createFromSession($session);
        if ($c) {
            $c->adminPassword = '';
            $c->storeInSession($session);
        }
        */
    }

    public function storeInSession(SessionInterface $session): void
    {
        $session->set('ConnectionDetails', $this);
    }


    public function getIsSSL(): bool
    {
        if (isset($this->parseUrl['scheme']) && $this->parseUrl['scheme'] === 'ldaps') {
            return true;
        }

        return false;
    }

    public function getPort(): int
    {
        if (isset($this->parseUrl['port'])) {
            return intval($this->parseUrl['port']);
        }

        if ($this->getIsSSL()) {
            return 636;
        }

        return 389;
    }

    public function getHost(): string
    {
        if (isset($this->parseUrl['host'])) {
            return $this->parseUrl['host'];
        }

        return 'localhost';
    }
}
