<?php

return [
    'ldap-sessions' => [
        [
            'LDAP' => [
                'dsn' => 'ldap://localhost:1389',
                'baseDn' => 'dc=example,dc=org',
                'adminDn' => 'cn=admin,dc=example,dc=org',
                'adminPassword' => 'secret',
            ],
            'UserManager' => [
                'enabled' => true,
            ],
        ],
        [
            'LDAP' => [
                'dsn' => 'ldap://example.com:389',
                'baseDn' => 'dc=example,dc=org',
                'adminDn' => 'cn=admin,dc=example,dc=org',
                'adminPassword' => 'secret',
            ],
        ],
    ]
];
