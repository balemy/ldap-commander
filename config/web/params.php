<?php

declare(strict_types=1);

use Yiisoft\ErrorHandler\Middleware\ErrorCatcher;
use Yiisoft\Router\Middleware\Router;
use Yiisoft\Session\SessionMiddleware;
use Yiisoft\Yii\Middleware\Subfolder;

return [
    'middlewares' => [
        ErrorCatcher::class,
        SessionMiddleware::class,
        Subfolder::class,
        Router::class,
    ]
];
