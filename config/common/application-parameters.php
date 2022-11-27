<?php

declare(strict_types=1);

use Balemy\LdapCommander\ApplicationParameters;

/** @var array $params */

return [
    ApplicationParameters::class => [
        'class' => ApplicationParameters::class,
        'charset()' => [$params['app']['charset']],
        'name()' => [$params['app']['name']],
        'version()' => [$params['app']['version']],
    ],
];
