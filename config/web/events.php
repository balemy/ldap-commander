<?php

declare(strict_types=1);

use Balemy\LdapCommander\Timer;
use Yiisoft\Yii\Http\Event\ApplicationStartup;

return [
    ApplicationStartup::class => [
        static fn(Timer $timer) => $timer->start('overall'),
    ],
];
