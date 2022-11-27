<?php

declare(strict_types=1);

use Balemy\LdapCommander\Command\Hello;
use Balemy\LdapCommander\ViewInjection\CommonViewInjection;
use Balemy\LdapCommander\ViewInjection\LayoutViewInjection;
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
        'version' => '0.6.0',
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
            '@messages' => '@root/resources/messages',
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

    'yiisoft/form' => [
        'configs' => [
            'default' => [
                'containerClass' => 'form-floating mb-3',
                'inputClass' => 'form-control',
                'invalidClass' => 'is-invalid',
                'validClass' => 'is-valid',
                'template' => '{input}{label}{hint}{error}',
                'labelClass' => 'floatingInput',
                'errorClass' => 'fw-bold fst-italic',
                'hintClass' => 'form-text',
                'fieldConfigs' => [
                    \Yiisoft\Form\Field\SubmitButton::class => [
                        'buttonClass()' => ['btn btn-primary btn-lg mt-3'],
                        'containerClass()' => ['d-grid gap-2 form-floating'],
                    ],
                ],
            ],
            'entity' => [
                'containerClass' => 'row mb-3',
                'labelClass' => 'col-sm-4 col-form-label',
                'inputContainerClass' => 'col-sm-8',
                'inputContainerTag' => 'div',
                'invalidClass' => 'is-invalid',
                'errorClass' => 'text-danger fst-italic',
                'hintClass' => 'form-text',
                'inputClass' => 'form-control',
                'validClass' => 'is-valid',
            ],
        ],
    ],

    'yiisoft/yii-console' => [
        'commands' => [
            'hello' => Hello::class,
        ],
    ],
];
