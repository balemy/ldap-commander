<?php

declare(strict_types=1);

use Balemy\LdapCommander\Asset\AppAsset;
use Balemy\LdapCommander\Widget\FlashMessage;use Balemy\LdapCommander\Widget\PerformanceMetrics;use Yiisoft\Html\Html;
use Yiisoft\I18n\Locale;
use Yiisoft\Strings\StringHelper;use Yiisoft\Yii\Bootstrap5\Nav;
use Yiisoft\Yii\Bootstrap5\NavBar;

/**
 * @var Balemy\LdapCommander\ApplicationParameters $applicationParameters
 * @var Yiisoft\Aliases\Aliases $aliases
 * @var Yiisoft\Assets\AssetManager $assetManager
 * @var string $content
 * @var string|null $csrf
 * @var Locale $locale
 * @var Yiisoft\View\WebView $this
 * @var Yiisoft\Router\UrlGeneratorInterface $urlGenerator
 * @var Yiisoft\Router\CurrentRoute $currentRoute
 * @var \Balemy\LdapCommander\Modules\Session\Session $session
 */

$assetManager->register(AppAsset::class);

$this->addCssFiles($assetManager->getCssFiles());
$this->addCssStrings($assetManager->getCssStrings());
$this->addJsFiles($assetManager->getJsFiles());
$this->addJsStrings($assetManager->getJsStrings());
$this->addJsVars($assetManager->getJsVars());

$currentRouteName = $currentRoute->getName() ?? '';

$this->beginPage()
?><!DOCTYPE html>
<html>
<head>
    <meta charset="<?= Html::encode($applicationParameters->getCharset()) ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= Html::encode($this->getTitle()) ?></title>
    <?php $this->head() ?>
</head>
<body class="d-flex flex-column h-100">
<?php $this->beginBody() ?>

<header>
    <?php
    $menuItems = [];

    if ($session->userManager->enabled) {
        $menuItems[] = [
            'label' => 'Users',
            'url' => $urlGenerator->generate('user-list'),
            'active' => StringHelper::startsWith($currentRouteName, 'user'),
        ];

        $menuItems[] = [
            'label' => 'Groups',
            'url' => $urlGenerator->generate('group-list'),
            'active' => StringHelper::startsWith($currentRouteName, 'group'),
        ];
    }


    $menuItems[] = [
        'label' => 'Browser',
        'url' => $urlGenerator->generate('entity-list'),
        'active' => StringHelper::startsWith($currentRouteName, 'entity'),
    ];

    /*
[
'label' => 'Configuration',
'items' => [
    [
        'label' => 'Schema Editor',
        'url' => $urlGenerator->generate('schema-edit'),
        'active' => StringHelper::startsWith($currentRouteName, 'schema-edit'),
    ],
    [
        'label' => 'Referential Integrity',
        'url' => $urlGenerator->generate('server-config-refint-edit'),
        'active' => StringHelper::startsWith($currentRouteName, 'server-config-refint-edit'),
    ],
    [
        'label' => 'Password Policies',
        'url' => $urlGenerator->generate('server'),
        'active' => StringHelper::startsWith($currentRouteName, 'server'),
    ],
    [
        'label' => 'Reverse Group Membership',
        'url' => $urlGenerator->generate('server-config-memberof-edit'),
        'active' => StringHelper::startsWith($currentRouteName, 'server-config-memberof-edit'),
    ],
    [
        'label' => 'Indexes',
        'url' => $urlGenerator->generate('server'),
        'active' => StringHelper::startsWith($currentRouteName, 'server'),
    ]
]
],
    */
    $menuItems[] = [
        'label' => 'Information',
        'items' => [
            [
                'label' => 'Schema',
                'url' => $urlGenerator->generate('schema'),
                'active' => StringHelper::startsWith($currentRouteName, 'schema'),
            ],
            [
                'label' => 'Server',
                'url' => $urlGenerator->generate('server'),
                'active' => StringHelper::startsWith($currentRouteName, 'server'),
            ]
        ]
    ];

    ?>

    <?= NavBar::widget()
        ->brandText($applicationParameters->getName())
        ->brandUrl('/')
        ->options([
            'class' => 'navbar navbar-dark bg-dark navbar-expand-lg text-white',
        ])
        ->begin();
    ?>

    <?= Nav::widget()
#        ->currentPath($currentPath)
        ->items($menuItems)
        ->options([
            'class' => 'navbar-nav me-auto'
        ]);
    ?>

    <div class="d-flex">
        <?= Nav::widget()
            ->items([[
                'label' => 'Logout',
                'url' => $urlGenerator->generate('logout'),
            ]])->options([
                'class' => 'navbar-nav ml-auto'
            ]);
        ?>
    </div>

    <?= NavBar::end() ?>

</header>


<main role="main" class="flex-shrink-0">
    <div class="container">
        <?= FlashMessage::widget() ?>

        <?= $content ?>
    </div>
</main>

<?= $this->render('_footer', ['applicationParameters' => $applicationParameters]); ?>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>