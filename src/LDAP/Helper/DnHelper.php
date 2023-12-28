<?php

namespace Balemy\LdapCommander\LDAP\Helper;

final class DnHelper
{

    /**
     * @param string $dn
     * @return string[]
     */
    public static function getRdns(string $dn): array
    {
        return explode(',', $dn);
    }

    /**
     * @param $rdn string
     * @return string
     * @throws \Exception
     */
    public static function getRdnAttributeName(string $rdn): string
    {
        return static::splitRdn($rdn)[0] ?? '';
    }

    /**
     * @param $rdn string
     * @return string
     * @throws \Exception
     */
    public static function getRdnAttributeValue(string $rdn): string
    {
        return static::splitRdn($rdn)[1] ?? '';
    }

    /**
     * @return string[]
     */
    private static function splitRdn(string $rdn): array
    {
        if (!str_contains($rdn, '=')) {
            throw new \Exception('Invalid RDN: ' . $rdn);
        }

        return explode('=', $rdn, 2);
    }

}
