<?php

declare(strict_types=1);

/**
 * @var \Yiisoft\View\WebView $this
 * @var \Balemy\LdapCommander\ApplicationParameters $applicationParameters
 * @var \Yiisoft\Router\UrlGeneratorInterface $urlGenerator
 * @var \Yiisoft\Router\CurrentRoute $currentRoute
 * @var Csrf $csrf
 * @var \Balemy\LdapCommander\User\User $user
 */

use Balemy\LdapCommander\User\SidebarLocation;
use Balemy\LdapCommander\User\SidebarWidget;
use Yiisoft\Form\Field;
use Yiisoft\Html\Html;
use Yiisoft\Html\Tag\Form;
use Yiisoft\Yii\View\Csrf;

$this->setTitle($applicationParameters->getName());
?>

<div class="row">
    <div class="col-md-9">
        <?php if (!$user->isNewRecord()): ?>
            <h1>Edit User</h1>
            <p class="lead">
                <?= Html::encode($user->getDn()) ?>
            </p>
        <?php else: ?>
            <h1>Create User</h1>
            <p class="lead">
            </p>
        <?php endif; ?>
        <br>

        <?= Html::form()->post($urlGenerator->generate('user-edit', ['dn' => $user->getDn()]))->csrf($csrf)->open() ?>

        <div class="row">
            <div class="col-sm-4">
                <?= Field::text($user, 'username')
                    ->autofocus()
                    ->tabindex(1) ?>
            </div>
            <div class="col-sm">
                <?= Field::text($user, 'commonName')
                    ->autofocus()
                    ->tabindex(2) ?>
            </div>
        </div>


        <div class="row">
            <div class="col-sm">
                <?= Field::text($user, 'firstName')
                    ->tabindex(2) ?>
            </div>
            <div class="col-sm">
                <?= Field::text($user, 'lastName')
                    ->tabindex(2) ?>
            </div>
            <div class="col-sm-2">
                <?= Field::text($user, 'title')
                    ->tabindex(2) ?>
            </div>
            <div class="col-sm-2">
                <?= Field::text($user, 'initials')
                    ->tabindex(2) ?>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <?= Field::text($user, 'mail')
                    ->tabindex(2) ?>
            </div>
            <div class="col">
                <?= Field::text($user, 'telephoneNumber')
                    ->tabindex(2) ?>
            </div>
            <div class="col">
                <?= Field::text($user, 'mobile')
                    ->tabindex(2) ?>
            </div>
        </div>


        <?= Field::submitButton()
            ->tabindex(3)
            ->content('Save') ?>

        <?= Form::tag()->close(); ?>
    </div>

    <div class="col-md-3">
        <?= SidebarWidget::widget(['$dn' => $user->getDn(), '$location' => SidebarLocation::Edit]); ?>
    </div>

</div>
