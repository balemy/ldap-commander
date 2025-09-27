<?php

declare(strict_types=1);

use Balemy\LdapCommander\Asset\AppAsset;
use Balemy\LdapCommander\Widget\FlashMessage;
use Yiisoft\Html\Html;
use Yiisoft\I18n\Locale;
use Yiisoft\Strings\StringHelper;use Yiisoft\Bootstrap5\Dropdown;use Yiisoft\Bootstrap5\DropdownItem;use Yiisoft\Bootstrap5\Nav;
use Yiisoft\Bootstrap5\NavBar; use Yiisoft\Bootstrap5\NavLink;

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

    <?= NavBar::widget()
        ->brandText($applicationParameters->getName())
        ->brandUrl('/')
        ->addClass('navbar', 'navbar-dark', 'bg-dark', 'navbar-expand-lg', 'text-white')
        ->begin();
    ?>

    <?= Nav::widget()
#        ->currentPath($currentPath)
        ->items(
            NavLink::to(
                'Users',
                $urlGenerator->generate('user-list'),
                StringHelper::startsWith($currentRouteName, 'user'),
                !$session?->userManager->enabled,
            ),
            NavLink::to(
                'Groups',
                $urlGenerator->generate('group-list'),
                StringHelper::startsWith($currentRouteName, 'group'),
                !$session?->userManager->enabled,
            ),
            NavLink::to(
                'Browser',
                $urlGenerator->generate('entity-list'),
                StringHelper::startsWith($currentRouteName, 'entity')
            ),
            Dropdown::widget()->togglerContent('Server Config')->items(
                DropdownItem::link(
                    'Modules',
                    $urlGenerator->generate('module-config'),
                    StringHelper::startsWith($currentRouteName, 'module-config')
                ),
                DropdownItem::link(
                    'Access Control',
                    $urlGenerator->generate('access-control'),
                    StringHelper::startsWith($currentRouteName, 'access-control')
                ),
            ),
            Dropdown::widget()->togglerContent('More')->items(
                DropdownItem::link(
                    'Schema Browser',
                    $urlGenerator->generate('schema'),
                    StringHelper::startsWith($currentRouteName, 'schema')
                ),
                DropdownItem::link(
                    'Server Info',
                    $urlGenerator->generate('server'),
                    StringHelper::startsWith($currentRouteName, 'server')
                ),
                DropdownItem::link(
                    'Raw Query',
                    $urlGenerator->generate('raw-query'),
                    StringHelper::startsWith($currentRouteName, 'raw-query')
                ),
                DropdownItem::link(
                    'Bind Users (Applications)',
                    $urlGenerator->generate('bind-user-list'),
                    StringHelper::startsWith($currentRouteName, 'bind-user')
                ),
            )
        )
    ->addClass('navbar-nav', 'me-auto')
    ?>

    <div class="d-flex">
        <?= Nav::widget()
            ->items(NavLink::to('Logout (' . $session?->connectionDetails->dsn . ')', $urlGenerator->generate('logout')))
            ->addClass('navbar-nav', 'ml-auto');
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

<?= $this->render('./_footer', ['applicationParameters' => $applicationParameters]); ?>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
