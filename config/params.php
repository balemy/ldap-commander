<?php

declare(strict_types=1);

use App\Command\Hello;
use App\ViewInjection\CommonViewInjection;
use App\ViewInjection\LayoutViewInjection;
use Yiisoft\Definitions\Reference;
use Yiisoft\ErrorHandler\Middleware\ErrorCatcher;
use Yiisoft\Router\Middleware\Router;
use Yiisoft\Session\SessionMiddleware;
use Yiisoft\Yii\View\CsrfViewInjection;

return [
    'app' => [
        'charset' => 'UTF-8',
        'locale' => 'en',
        'name' => 'LDAP Commander',
        'version' => '0.1.0',
    ],
    'middlewares' => [
        ErrorCatcher::class,
        SessionMiddleware::class,
        Router::class,
    ],

    'yiisoft/aliases' => [
        'aliases' => [
            '@root' => dirname(__DIR__),
            '@assets' => '@root/public/assets',
            '@assetsUrl' => '@baseUrl/assets',
            '@baseUrl' => '/',
            '@message' => '@root/resources/message',
#            '@npm' => '@root/node_modules',
            '@public' => '@root/public',
            '@resources' => '@root/resources',
            '@runtime' => '@root/runtime',
            '@vendor' => '@root/vendor',
            '@layout' => '@resources/views/layout',
            '@views' => '@resources/views',
            '@bower' => '@vendor/bower-asset',
            '@npm' => '@vendor/npm-asset',
        ],
    ],

    'yiisoft/yii-view' => [
        'injections' => [
            Reference::to(CommonViewInjection::class),
            Reference::to(CsrfViewInjection::class),
            Reference::to(LayoutViewInjection::class),
        ],
    ],


    'yiisoft/forms' => [
        'field' => [
            'ariaDescribedBy' => [true],
            'containerClass' => ['form-floating mb-3'],
            'errorClass' => ['fw-bold fst-italic invalid-feedback'],
            'hintClass' => ['form-text'],
            'inputClass' => ['form-control'],
            'invalidClass' => ['is-invalid'],
            'labelClass' => ['floatingInput'],
            'template' => ['{input}{label}{hint}{error}'],
            'validClass' => ['is-valid'],
            'defaultValues' => [
                [
                    'submit' => [
                        'definitions' => [
                            'class()' => ['btn btn-primary btn-lg mt-3'],
                        ],
                        'containerClass' => 'd-grid gap-2 form-floating',
                    ],
                ],
            ],
        ],
        'form' => [
            'attributes' => [['enctype' => 'multipart/form-data']],
        ],
    ],


    'yiisoft/yii-console' => [
        'commands' => [
            'hello' => Hello::class,
        ],
    ],
];
