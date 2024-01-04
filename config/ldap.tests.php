<?php

return [
    'app' => [
        'loginMessage' => '<b>Demo</b> - Leave username empty, password is "secret".  Demo will be reset every hour.<br>'
    ],
    'ldap-sessions' => [
        [
            'title' => 'Test Docker',
            'LDAP' => [
                'dsn' => 'ldap://localhost:1389',
                'baseDn' => 'dc=example,dc=org',
                'adminDn' => 'cn=admin,dc=example,dc=org',
                'adminPassword' => 'secret',
                'configDn' => 'cn=admin,cn=config',
                'configPassword' => 'configpassword'
            ],
            'UserManager' => [
                'enabled' => true,
            ],
            'GroupManager' => [
                'enabled' => true
            ],
            'EntityBrowser' => [

            ],
        ],
    ]
];
