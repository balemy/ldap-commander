<?php

declare(strict_types=1);

/**
 * @var \Yiisoft\View\WebView $this
 * @var \Balemy\LdapCommander\ApplicationParameters $applicationParameters
 * @var \Yiisoft\Router\UrlGeneratorInterface $urlGenerator
 * @var \Yiisoft\Router\CurrentRoute $currentRoute
 * @var Csrf $csrf
 * @var string $dn
 * @var \Balemy\LdapCommander\Group\Group[] $assignedGroups
 * @var \Balemy\LdapCommander\Group\Group[] $notAssignedGroups
 */

use Balemy\LdapCommander\User\SidebarWidget;
use Balemy\LdapCommander\User\SidebarLocation;
use Yiisoft\Html\Tag\Button;
use Yiisoft\Html\Tag\Form;
use Yiisoft\Html\Tag\Input;
use Yiisoft\Html\Tag\Select;
use Yiisoft\Yii\View\Csrf;

$this->setTitle($applicationParameters->getName());

$addOptions = ['' => ''];
foreach ($notAssignedGroups as $group) {
    $addOptions[$group->getDn()] = $group->getTitle() . ' (' . $group->getDescription() . ')';
}

$form = Form::tag()
    ->method('post')
    ->csrf($csrf)
    ->action($urlGenerator->generate('user-groups', ['dn' => $dn]))
?>


<div class="row">
    <div class="col-md-9">
        <h1>Group Memberships (<?= count($assignedGroups); ?>)</h1>
        <p class="lead">
            <?= $dn ?>
        </p>
        <br>

        <?= $form->addClass('row g-3 alert alert-dark')->open(); ?>
        <div class="col-auto">
            <label for="select2add" class="visually-hidden">Add new Group Membership</label>
            <?= Select::tag()
                ->optionsData($addOptions)
                ->id("select2add")
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
                <th scope="col">Title</th>
                <th scope="col">Description</th>
                <!--<th scope="col">DN</th>-->
                <th>&nbsp;</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($assignedGroups as $group): ?>
                <tr>
                    <td data-checkbox="true"></td>
                    <td><?= $group->getTitle(); ?></td>
                    <td><?= $group->getDescription(); ?></td>
                    <!--<td><?= $group->getDn(); ?></td>-->
                    <td>
                        <?= $form->open() ?>
                        <?= Input::hidden('delDn', $group->getDn()); ?>
                        <?= Input::submitButton('Remove')->addClass('btn btn-primary btn-sm'); ?>
                        <?= $form->close() ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="col-md-3">
        <?= SidebarWidget::widget(['$dn' => $dn, '$location' => SidebarLocation::Members]); ?>
    </div>
</div>
<script>
    $(document).ready(function () {
        $('#select2add').select2({
            theme: 'bootstrap-5',
            placeholder: "Select Group to add"
        });
    });
</script>
