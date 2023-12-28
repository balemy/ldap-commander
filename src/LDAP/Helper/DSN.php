<?php

namespace Balemy\LdapCommander\LDAP\Helper;

class DSN
{
    /**
     * @var array{fragment?: string, host?: string, pass?: string, path?: string, port?: int, query?: string, scheme?: string, user?: string}|false
     */
    private $parseUrl = [];

    public function __construct(public string $dsn)
    {
        $this->parseUrl = parse_url($this->dsn);
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


    public function getIsSSL(): bool
    {
        if (isset($this->parseUrl['scheme']) && $this->parseUrl['scheme'] === 'ldaps') {
            return true;
        }

        return false;
    }
}
