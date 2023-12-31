<?php

declare(strict_types=1);

use Balemy\LdapCommander\Modules\Session\ConfiguredSessionList;

/** @var array $params */

return [
    ConfiguredSessionList::class => [
        '__construct()' => [
            'params' => $params['ldap-sessions'] ?? [],
        ],
    ],
];
