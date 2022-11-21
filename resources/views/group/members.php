<?php

declare(strict_types=1);

/**
 * @var \Yiisoft\View\WebView $this
 * @var \App\ApplicationParameters $applicationParameters
 * @var \Yiisoft\Router\UrlGeneratorInterface $urlGenerator
 * @var \Yiisoft\Router\CurrentRoute $currentRoute
 * @var Csrf $csrf
 * @var string $dn
 * @var User[] $members
 * @var User[] $noMembers
 */

use App\Ldap\User;
use App\Widget\GroupSidebar;
use App\Widget\GroupSidebarLocation;
use Yiisoft\Html\Html;
use Yiisoft\Html\Tag\Button;
use Yiisoft\Html\Tag\Form;
use Yiisoft\Html\Tag\Input;
use Yiisoft\Html\Tag\Select;
use Yiisoft\Yii\View\Csrf;

$this->setTitle($applicationParameters->getName());

$addOptions = ['' => ''];
foreach ($noMembers as $user) {
    $addOptions[$user->getDn()] = $user->getDisplayName() . ' (' . $user->getUsername() . ')';
}

$form = Form::tag()
    ->method('post')
    ->csrf($csrf)
    ->action($urlGenerator->generate('group-members', ['dn' => $dn]))
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
                <th scope="col">ID</th>
                <th scope="col">Username</th>
                <th scope="col">Display Name</th>
                <th scope="col">E-Mail</th>
                <th>&nbsp;</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($members as $user): ?>
                <?php
                $editUrl = $urlGenerator->generate('entity-edit', ['dn' => $user->getDn()]);
                ?>
                <tr>
                    <td data-checkbox="true"></td>
                    <td><?= ($user->getId()) ? Html::a($user->getId(), $editUrl) : ''; ?></td>
                    <td><?= $user->getUsername(); ?></td>
                    <td><?= $user->getFirstName() ?? ''; ?> <?= $user->getLastName(); ?></td>
                    <td><?= ($user->getMail() !== null) ? Html::mailto($user->getMail(), $user->getMail()) : '' ?></td>
                    <td>
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
        <?= GroupSidebar::widget(['$dn' => $dn, '$location' => GroupSidebarLocation::Members]); ?>
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
