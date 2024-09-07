<?php

declare(strict_types=1);

/**
 * @var \Yiisoft\View\WebView $this
 * @var \Balemy\LdapCommander\ApplicationParameters $applicationParameters
 * @var \Yiisoft\Router\UrlGeneratorInterface $urlGenerator
 * @var \Yiisoft\Router\CurrentRoute $currentRoute
 * @var Csrf $csrf
 * @var string $dn
 * @var string[] $parentDNs
 * @var \Balemy\LdapCommander\Modules\SlapdConfig\Models\BindUser $bindUser
 */

use Balemy\LdapCommander\Modules\SlapdConfig\Widgets\BindUserSidebarWidget;
use Yiisoft\FormModel\Field;
use Yiisoft\Html\Html;
use Yiisoft\Html\Tag\Form;
use Yiisoft\Yii\View\Renderer\Csrf;

$this->setTitle($applicationParameters->getName());
?>

<div class="row">
    <div class="col-md-9">
        <?php if (!$bindUser->isNewRecord): ?>
            <h1>Edit Bind User</h1>
            <p class="lead">
                <?= Html::encode($bindUser->getDn()) ?>
            </p>
        <?php else: ?>
            <h1>Create Bind User</h1>
        <?php endif; ?>
        <br>

        <?= Html::form()->post($urlGenerator->generate('bind-user-edit', [], ['dn' => $dn]))->csrf($csrf)->open() ?>

        <?= Field::text($bindUser, 'cn')
            ->autofocus()
            ->tabindex(1) ?>

        <?= Field::textarea($bindUser, 'description')
            ->addInputAttributes(['style' => 'height:150px'])
            ->tabindex(2) ?>

        <?= Field::text($bindUser, 'userPassword')
            ->autofocus()
            ->tabindex(3) ?>

        <?= Field::select($bindUser, 'parentDn')
            ->optionsData($parentDNs)
            ->tabindex(4) ?>

        <?= Field::submitButton()
            ->tabindex(5)
            ->content('Save') ?>

        <?= Form::tag()->close(); ?>
    </div>
    <div class="col-md-3">
        <?= BindUserSidebarWidget::widget([], ['$dn' => $dn]); ?>
    </div>
</div>
