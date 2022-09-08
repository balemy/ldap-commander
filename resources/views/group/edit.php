<?php

declare(strict_types=1);

/**
 * @var \Yiisoft\View\WebView $this
 * @var \App\ApplicationParameters $applicationParameters
 * @var \Yiisoft\Router\UrlGeneratorInterface $urlGenerator
 * @var \Yiisoft\Router\CurrentRoute $currentRoute
 * @var Csrf $csrf
 * @var string $dn
 * @var \App\Ldap\GroupForm $formModel
 */

use App\Widget\GroupSidebar;
use App\Widget\GroupSidebarLocation;
use Yiisoft\Form\Field;
use Yiisoft\Html\Html;
use Yiisoft\Html\Tag\Form;
use Yiisoft\Yii\View\Csrf;

$this->setTitle($applicationParameters->getName());
?>

<div class="row">
    <div class="col-md-9">
        <h1>Edit Group</h1>
        <p class="lead">
            <?= Html::encode($dn); ?>
        </p>
        <br>

        <?= Html::form()->post($urlGenerator->generate('group-edit', ['dn' => $dn]))->csrf($csrf)->open() ?>

        <?= Field::text($formModel, 'title')
            ->autofocus()
            ->tabindex(1) ?>

        <?= Field::textarea($formModel, 'description')
            ->addInputAttributes(['style' => 'height:150px'])
            ->tabindex(2) ?>

        <?= Field::submitButton()
            ->tabindex(3)
            ->content('Save') ?>

        <?= Form::tag()->close(); ?>
    </div>

    <div class="col-md-3">
        <?= GroupSidebar::widget(['$dn' => $dn, '$location' => GroupSidebarLocation::Edit]); ?>
    </div>

</div>
