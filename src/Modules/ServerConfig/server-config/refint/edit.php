<?php

declare(strict_types=1);

/**
 * @var \Yiisoft\View\WebView $this
 * @var \Balemy\LdapCommander\ApplicationParameters $applicationParameters
 * @var \Yiisoft\Router\UrlGeneratorInterface $urlGenerator
 * @var \Yiisoft\Router\CurrentRoute $currentRoute
 * @var Csrf $csrf
 * @var string $dn
 * @var string[] $parentDns
 * @var \Balemy\LdapCommander\Modules\GroupManager\GroupForm $formModel
 */

use Balemy\LdapCommander\Group\SidebarWidget;
use Yiisoft\FormModel\Field;
use Yiisoft\Html\Html;
use Yiisoft\Html\Tag\Form;
use Yiisoft\Yii\View\Csrf;

$this->setTitle($applicationParameters->getName());
?>

<div class="row">
    <div class="col-md-9">
        <h1>Edit RefInt</h1>
        <p class="lead">

        </p>
        <br>

        <?= Html::form()->post($urlGenerator->generate('server-config-refint-edit'))->csrf($csrf)->open() ?>

        <?= Field::checkbox($formModel, 'enabled')
            ->tabindex(1) ?>

        <?= Field::text($formModel, 'refintAttribute')
            ->tabindex(2) ?>

        <?= Field::text($formModel, 'refintNothing')
            ->tabindex(3) ?>

        <?= Field::submitButton()
            ->tabindex(4)
            ->content('Save') ?>

        <?= Form::tag()->close(); ?>
    </div>

    <div class="col-md-3">
    </div>

</div>
