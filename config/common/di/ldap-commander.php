<?php

declare(strict_types=1);

use Balemy\LdapCommander\Modules\Session\SessionList;

/** @var array $params */

return [
    SessionList::class => [
        '__construct()' => [
            'params' => $params['ldap-sessions'] ?? [],
        ],
    ],
];
