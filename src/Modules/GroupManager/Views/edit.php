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

use Balemy\LdapCommander\Modules\GroupManager\SidebarWidget;
use Balemy\LdapCommander\Modules\GroupManager\GroupSidebarLocation;
use Yiisoft\FormModel\Field;
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

        <?= Html::form()->post($urlGenerator->generate('group-edit', [], ['dn' => $dn]))->csrf($csrf)->open() ?>

        <?= Field::text($formModel, 'title')
            ->autofocus()
            ->tabindex(1) ?>

        <?= Field::textarea($formModel, 'description')
            ->addInputAttributes(['style' => 'height:150px'])
            ->tabindex(2) ?>

        <?= Field::select($formModel, 'parentDn')
            ->optionsData($parentDns)
            ->tabindex(3) ?>

        <?= Field::submitButton()
            ->tabindex(3)
            ->content('Save') ?>

        <?= Form::tag()->close(); ?>
    </div>

    <div class="col-md-3">
        <?= SidebarWidget::widget([], ['$dn' => $dn, '$location' => GroupSidebarLocation::Edit]); ?>
    </div>

</div>
