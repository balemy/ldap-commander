<?php

declare(strict_types=1);

use App\Asset\AppAsset;
use App\Widget\FlashMessage;use Yiisoft\Html\Html;
use Yiisoft\I18n\Locale;
use Yiisoft\Yii\Bootstrap5\Nav;
use Yiisoft\Yii\Bootstrap5\NavBar;

/**
 * @var App\ApplicationParameters $applicationParameters
 * @var Yiisoft\Aliases\Aliases $aliases
 * @var Yiisoft\Assets\AssetManager $assetManager
 * @var string $content
 * @var string|null $csrf
 * @var Locale $locale
 * @var Yiisoft\View\WebView $this
 * @var Yiisoft\Router\UrlGeneratorInterface $urlGenerator
 * @var Yiisoft\Router\CurrentRoute $currentRoute
 */

$assetManager->register(AppAsset::class);

$this->addCssFiles($assetManager->getCssFiles());
$this->addCssStrings($assetManager->getCssStrings());
$this->addJsFiles($assetManager->getJsFiles());
$this->addJsStrings($assetManager->getJsStrings());
$this->addJsVars($assetManager->getJsVars());

$this->beginPage()
?><!DOCTYPE html>
<html lang="<?= Html::encode($locale->language()) ?>">
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
        ->options([
            'class' => 'navbar navbar-dark bg-dark navbar-expand-lg text-white',
        ])
        ->begin();
    ?>

    <?= NavBar::end() ?>

</header>


<main role="main" class="flex-shrink-0">
    <div class="container">
        <?= FlashMessage::widget() ?>

        <?= $content ?>
    </div>
</main>


<footer class="footer mt-auto py-3 text-muted">
    <div class="container">
        <p>
            <?= $applicationParameters->getVersion() ?>
        </p>
    </div>
</footer>


<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
