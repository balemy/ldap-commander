<?php

namespace Balemy\LdapCommander\LDAP\Helper;

class OID
{

    /**
     * @var string[]
     */
    private static $descriptions = [
        '1.2.826.0.1.3344810.2.3' => 'Matched Values Control',
        '1.2.840.113556.1.4.319' => 'LDAP Simple Paged Results Control',
        '1.2.840.113556.1.4.473' => 'Sort Request',
        '1.2.840.113556.1.4.474' => 'Sort Response',
        '1.3.6.1.1.7.1' => 'LCUP Sync Request Control',
        '1.3.6.1.1.7.2' => 'LCUP Sync Update Control',
        '1.3.6.1.1.7.3' => 'LCUP Sync Done Control',
        '1.3.6.1.1.8' => 'Cancel Operation',
        '1.3.6.1.1.12' => 'Assertion Control',
        '1.3.6.1.1.13.1' => 'LDAP Pre-read Control',
        '1.3.6.1.1.13.2' => 'LDAP Post-read Control',
        '1.3.6.1.1.14' => 'Modify-Increment',
        '1.3.6.1.1.21.1' => 'Start Transaction Extended Request',
        '1.3.6.1.1.21.2' => 'Transaction Specification Control',
        '1.3.6.1.1.21.3' => 'End Transaction Extended Request',
        '1.3.6.1.1.21.4' => 'Aborted Transaction Notice',
        '1.3.6.1.4.1.1466.101.119.1' => 'Dynamic Refresh',
        '1.3.6.1.4.1.1466.20037' => 'StartTLS',
        '1.3.6.1.4.1.4203.1.5.1' => 'All Op Attrs',
        '1.3.6.1.4.1.4203.1.5.2' => 'OC AD Lists',
        '1.3.6.1.4.1.4203.1.5.3' => 'True/False filters',
        '1.3.6.1.4.1.4203.1.5.4' => 'Language Tag Options',
        '1.3.6.1.4.1.4203.1.5.5' => 'Language Range Options',
        '1.3.6.1.4.1.4203.1.9.1.1' => 'LDAP Content Synchronization Control',
        '1.3.6.1.4.1.4203.1.10.1' => 'Subentries',
        '1.3.6.1.4.1.4203.1.11.1' => 'Modify Password',
        '1.3.6.1.4.1.4203.1.11.3' => 'Who am I?',
        '1.3.6.1.1.17.1' => 'StartLBURPRequest LDAP ExtendedRequest message',
        '1.3.6.1.1.17.2' => 'StartLBURPResponse LDAP ExtendedResponse message',
        '1.3.6.1.1.17.3' => 'EndLBURPRequest LDAP ExtendedRequest message',
        '1.3.6.1.1.17.4' => 'EndLBURPResponse LDAP ExtendedResponse message',
        '1.3.6.1.1.17.5' => 'LBURPUpdateRequest LDAP ExtendedRequest message',
        '1.3.6.1.1.17.6' => 'LBURPUpdateResponse LDAP ExtendedResponse message',
        '1.3.6.1.1.17.7' => 'LBURP Incremental Update style OID',
        '1.3.6.1.1.19' => 'LDAP Turn Operation',
        '2.16.840.1.113730.3.4.2' => 'ManageDsaIT',
        '2.16.840.1.113730.3.4.15' => 'Authorization Identity Response Control',
        '2.16.840.1.113730.3.4.16' => 'Authorization Identity Request Control',
        '2.16.840.1.113730.3.4.18' => 'Proxy Authorization Control',
        '1.3.6.1.1.22' => 'LDAP Don\'t Use Copy Control',
    ];

    public static function hasDescription(string $oid): bool
    {
        return array_key_exists($oid, static::$descriptions);
    }

    public static function getsDescription(string $oid): string
    {
        return static::$descriptions[$oid] ?? '';
    }

}
