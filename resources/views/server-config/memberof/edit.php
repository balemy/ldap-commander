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
 * @var \Balemy\LdapCommander\ServerConfig\MemberOf\MemberOfForm $formModel
 */

use Yiisoft\Form\Field;
use Yiisoft\Html\Html;
use Yiisoft\Html\Tag\Form;
use Yiisoft\Yii\View\Csrf;

$this->setTitle($applicationParameters->getName());
?>

<div class="row">
    <div class="col-md-9">
        <h1>Reverse Group Membership Maintenance</h1>
        <p class="lead">
            memberOf Module
        </p>
        <br>

        <?= Html::form()->post($urlGenerator->generate('server-config-memberof-edit'))->csrf($csrf)->open() ?>

        <?= Field::checkbox($formModel, 'enabled')
            ->containerClass('form-check form-switch')
            ->inputClass('form-check-input')
            ->inputLabelClass('form-check-label') ?>
        <br/>

        <?= Field::text($formModel, 'memberOfAD')
            ->tabindex(2) ?>

        <?= Field::text($formModel, 'memberAD')
            ->tabindex(3) ?>

        <?= Field::text($formModel, 'groupOC')
            ->tabindex(3) ?>

        <?= Field::submitButton()
            ->tabindex(4)
            ->content('Save') ?>

        <?= Form::tag()->close(); ?>
    </div>

    <div class="col-md-3">
    </div>

</div>
