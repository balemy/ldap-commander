<?php

declare(strict_types=1);

use Balemy\LdapCommander\ViewInjection\CommonViewInjection;
use Balemy\LdapCommander\ViewInjection\LayoutViewInjection;
use Yiisoft\Assets\AssetManager;
use Yiisoft\Definitions\Reference;
use Yiisoft\Form\Field\SubmitButton;
use Yiisoft\Router\CurrentRoute;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Yii\View\CsrfViewInjection;

return [
    'app' => [
        'charset' => 'UTF-8',
        'name' => 'LDAP Commander',
        'version' => '0.6.2',
    ],
    'yiisoft/aliases' => [
        'aliases' => [
            '@root' => dirname(__DIR__, 2),
            '@assets' => '@root/public/assets',
            '@assetsUrl' => '@baseUrl/assets',
            '@baseUrl' => '',
            '@messages' => '@resources/messages',
            '@npm' => '@root/node_modules',
            '@public' => '@root/public',
            '@resources' => '@root/resources',
            '@runtime' => '@root/runtime',
            '@src' => '@root/src',
            '@vendor' => '@root/vendor',
            '@layout' => '@views/layout',
            '@views' => '@src/Views',
        ],
    ],
    'yiisoft/form' => [
        'themes' => [
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
                    SubmitButton::class => [
                        'buttonClass()' => ['btn btn-primary btn-lg mt-3'],
                        'containerClass()' => ['d-grid gap-2 form-floating'],
                    ],
                ],
            ],
            'label-left' => [
                'containerClass' => 'row mb-3',
                'labelClass' => 'col-sm-4 col-form-label',
                'inputContainerClass' => 'col-sm-8',
                'inputContainerTag' => 'div',
                'invalidClass' => 'is-invalid',
                'errorClass' => 'text-danger fst-italic',
                'hintClass' => 'form-text',
                'inputClass' => 'form-control',
                'validClass' => 'is-valid',
            ]
        ],
    ],

    'yiisoft/router-fastroute' => [
        'enableCache' => false,
    ],

    'yiisoft/view' => [
        'basePath' => '@views',
        'parameters' => [
            'assetManager' => Reference::to(AssetManager::class),
            'urlGenerator' => Reference::to(UrlGeneratorInterface::class),
            'currentRoute' => Reference::to(CurrentRoute::class),
            'translator' => Reference::to(TranslatorInterface::class),
        ],
    ],

    'yiisoft/yii-view' => [
        'viewPath' => '@views',
        'layout' => '@views/layout/main',
        'injections' => [
            Reference::to(CommonViewInjection::class),
            Reference::to(CsrfViewInjection::class),
            Reference::to(LayoutViewInjection::class),
        ],
    ],

    'yiisoft/yii-debug-api' => [
        'allowedIPs' => ['172.0.0.1/10'],
    ],
];
