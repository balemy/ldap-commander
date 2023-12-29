<?php

declare(strict_types=1);

/**
 * @var \Yiisoft\View\WebView $this
 * @var \Balemy\LdapCommander\ApplicationParameters $applicationParameters
 * @var \Yiisoft\Router\UrlGeneratorInterface $urlGenerator
 * @var \Yiisoft\Router\CurrentRoute $currentRoute
 * @var \Balemy\LdapCommander\Modules\GroupManager\Group[] $groups
 */

use Yiisoft\Html\Html;

$this->setTitle($applicationParameters->getName());

?>

<style>
    .table td.fit,
    .table th.fit {
        white-space: nowrap;
        width: 1%;
    }
</style>
<div class="row">
    <div class="col-md-12">

        <h1>Groups</h1>
        <a href="<?= $urlGenerator->generate('group-add'); ?>" class="btn btn-success float-end">Create new group</a>
        <p class="lead">
            Overview of all groups
        </p>

        <br>
        <table class="table table-striped" data-search="true" data-toggle="table" data-pagination="true"
               data-page-size="100" data-sortable="true" data-click-to-select="true">
            <thead>
            <tr>
                <th data-checkbox="true"></th>
                <th class="fit" scope="col">Name</th>
                <th scope="col">Description</th>
                <th scope="col">Members</th>
                <th>&nbsp;</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($groups as $group): ?>
                <?php
                $editUrl = $urlGenerator->generate('group-edit', [], ['dn' => $group->getDn()]);
                $membersUrl = $urlGenerator->generate('group-members', [], ['dn' => $group->getDn()]);
                ?>
                <tr>
                    <td data-checkbox="true"></td>
                    <td class="fit"><?= $group->getTitle() ?? ''; ?></td>
                    <td><?= Html::encode($group->getDescription()); ?></td>
                    <td style="width:100px"><?= count($group->getUserDns()); ?></td>
                    <td style="width:150px">
                        <?= Html::a('Edit', $editUrl, ['class' => 'btn btn-secondary btn-sm']); ?>
                        <?= Html::a('Members', $membersUrl, ['class' => 'btn btn-secondary btn-sm']); ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

    </div>
</div>
