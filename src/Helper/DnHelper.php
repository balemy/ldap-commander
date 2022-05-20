<?php

namespace App\Helper;

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
        if (!str_contains($rdn, '=')) {
            throw new \Exception('Invalid RDN: ' . $rdn);
        }

        $parts = explode('=', $rdn, 2);
        return $parts[0] ?? '';
    }

}
