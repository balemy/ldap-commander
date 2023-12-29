<?php

declare(strict_types=1);

/**
 * @var \Yiisoft\View\WebView $this
 * @var \Balemy\LdapCommander\ApplicationParameters $applicationParameters
 * @var \Yiisoft\Router\UrlGeneratorInterface $urlGenerator
 * @var \Yiisoft\Router\CurrentRoute $currentRoute
 * @var Csrf $csrf
 * @var \Balemy\LdapCommander\Modules\UserManager\UserForm $userForm
 * @var string[] $parentDNs
 * @var string[] $fieldSet
 */

use Balemy\LdapCommander\Modules\UserManager\SidebarWidget;
use Balemy\LdapCommander\Modules\UserManager\SidebarLocation;
use Yiisoft\FormModel\Field;
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

        <?= Html::form()->post($urlGenerator->generate('user-edit', [], ['dn' => $userForm->user->getDn()]))->csrf($csrf)->open() ?>

        <?php foreach ($userForm->getFormSchema()->getRows() as $row): ?>
            <div class="row">
                <?php foreach ($row as $fieldName => $fieldLabel): ?>
                    <div class="col-sm">
                        <?php if ($fieldName === 'userPassword'): ?>
                            <?= Field::password($userForm, $fieldName)
                                ->autofocus() ?>
                        <?php else: ?>
                            <?= Field::text($userForm, $fieldName) ?>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
        <div class="row">
            <div class="col-sm-12">
                <?= Field::select($userForm, 'parentDn')
                    ->optionsData($parentDNs)
                    ->autofocus()
                    ->tabindex(1) ?>
            </div>
        </div>
        <?= Field::submitButton()
            ->tabindex(3)
            ->content('Save') ?>

        <?= Form::tag()->close(); ?>
    </div>

    <div class="col-md-3">
        <?= SidebarWidget::widget([], ['$user' => $userForm->user, '$location' => SidebarLocation::Edit]); ?>
    </div>

</div>
