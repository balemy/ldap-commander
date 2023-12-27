<?php

declare(strict_types=1);

/**
 * @var \Yiisoft\View\WebView $this
 * @var \Balemy\LdapCommander\ApplicationParameters $applicationParameters
 * @var \Yiisoft\Router\UrlGeneratorInterface $urlGenerator
 * @var \Yiisoft\Router\CurrentRoute $currentRoute
 * @var Csrf $csrf
 * @var string $dn
 * @var \Balemy\LdapCommander\Modules\UserManager\User[] $members
 * @var \Balemy\LdapCommander\Modules\UserManager\User[] $noMembers
 */

use Balemy\LdapCommander\Modules\GroupManager\SidebarWidget;
use Balemy\LdapCommander\Modules\GroupManager\GroupSidebarLocation;
use Yiisoft\Html\Html;
use Yiisoft\Html\Tag\Button;
use Yiisoft\Html\Tag\Form;
use Yiisoft\Html\Tag\Input;
use Yiisoft\Html\Tag\Select;
use Yiisoft\Yii\View\Csrf;

$this->setTitle($applicationParameters->getName());

$addOptions = ['' => ''];
foreach ($noMembers as $user) {
    $addOptions[$user->getDn()] = $user->getDisplayName();
}

$form = Form::tag()
    ->method('post')
    ->csrf($csrf)
    ->action($urlGenerator->generate('group-members', [], ['dn' => $dn]))
?>


<div class="row">
    <div class="col-md-9">
        <h1>Group Members (<?= count($members); ?>)</h1>
        <p class="lead">
            <?= $dn ?>
        </p>
        <br>

        <?= $form->addClass('row g-3 alert alert-dark')->open(); ?>
        <div class="col-auto">
            <label for="select2addUser" class="visually-hidden">Add new member</label>
            <?= Select::tag()
                ->optionsData($addOptions)
                ->id("select2addUser")
                ->name('addDn')
                ->addAttributes(['style' => 'width:300px'])
                ->addClass('form-control');
            ?>
        </div>
        <div class="col-auto">
            <?= Button::submit('Add')->addClass('btn btn-primary'); ?>
        </div>
        <?= Form::tag()->close() ?>

        <table class="table table-striped" data-search="true" data-toggle="table" data-pagination="true"
               data-page-size="100" data-sortable="true" data-click-to-select="true">
            <thead>
            <tr>
                <th data-checkbox="true"></th>
                <th scope="col">Display Name</th>
                <th scope="col">E-Mail</th>
                <th scope="col">&nbsp;</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($members as $user): ?>
                <?php
                $editUrl = $urlGenerator->generate('entity-edit', [], ['dn' => $user->getDn()]);
                ?>
                <tr>
                    <td data-checkbox="true"></td>
                    <td><?= Html::a($user->getDisplayName(), $urlGenerator->generate('user-edit', [], ['dn' => $user->getDn()])); ?></td>
                    <td>
                        <?php if ($user->getFirstAttribute('mail') !== null) : ?>
                            <?= Html::mailto($user->getFirstAttribute('mail')); ?>
                        <?php endif; ?>
                    </td>
                    <td style="width:100px">
                        <?= $form->open() ?>
                        <?= Input::hidden('delDn', $user->getDn()); ?>
                        <?= Input::submitButton('Remove')->addClass('btn btn-primary btn-sm'); ?>
                        <?= $form->close() ?>

                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="col-md-3">
        <?= SidebarWidget::widget(['$dn' => $dn, '$location' => GroupSidebarLocation::Members]); ?>
    </div>
</div>
<script>
    $(document).ready(function () {
        $('#select2addUser').select2({
            theme: 'bootstrap-5',
            placeholder: "Select user to add"
        });
    });

</script>
