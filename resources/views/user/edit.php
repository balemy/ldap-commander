<?php

declare(strict_types=1);

/**
 * @var \Yiisoft\View\WebView $this
 * @var \Balemy\LdapCommander\ApplicationParameters $applicationParameters
 * @var \Yiisoft\Router\UrlGeneratorInterface $urlGenerator
 * @var \Yiisoft\Router\CurrentRoute $currentRoute
 * @var Csrf $csrf
 * @var \Balemy\LdapCommander\User\UserForm $userForm
 * @var string[] $parentDNs
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
        <?php if (!$userForm->isNewRecord()): ?>
            <h1>Edit User</h1>
            <p class="lead">
                <?= Html::encode($userForm->user->getDn()) ?>
            </p>
        <?php else: ?>
            <h1>Create User</h1>
            <p class="lead">
            </p>
        <?php endif; ?>
        <br>

        <?= Html::form()->post($urlGenerator->generate('user-edit', ['dn' => $userForm->user->getDn()]))->csrf($csrf)->open() ?>

        <div class="row">
            <div class="col-sm-4">
                <?= Field::text($userForm, 'uid')
                    ->autofocus()
                    ->tabindex(1) ?>
            </div>
            <div class="col-sm">
                <?= Field::text($userForm, 'cn')
                    ->autofocus()
                    ->tabindex(2) ?>
            </div>
        </div>


        <div class="row">
            <div class="col-sm">
                <?= Field::text($userForm, 'givenName')
                    ->tabindex(2) ?>
            </div>
            <div class="col-sm">
                <?= Field::text($userForm, 'sn')
                    ->tabindex(2) ?>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <?= Field::text($userForm, 'mail')
                    ->tabindex(2) ?>
            </div>
        </div>
        <div class="col-sm-12">
            <?= Field::select($userForm, 'parentDn')
                ->optionsData($parentDNs)
                ->autofocus()
                ->tabindex(1) ?>
        </div>


        <?= Field::submitButton()
            ->tabindex(3)
            ->content('Save') ?>

        <?= Form::tag()->close(); ?>
    </div>

    <div class="col-md-3">
        <?= SidebarWidget::widget(['$dn' => $userForm->user->getDn() ?? '', '$location' => SidebarLocation::Edit]); ?>
    </div>

</div>
