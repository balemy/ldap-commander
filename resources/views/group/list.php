<?php

declare(strict_types=1);

/**
 * @var \Yiisoft\View\WebView $this
 * @var \Balemy\LdapCommander\ApplicationParameters $applicationParameters
 * @var \Yiisoft\Router\UrlGeneratorInterface $urlGenerator
 * @var \Yiisoft\Router\CurrentRoute $currentRoute
 * @var \Balemy\LdapCommander\Group\Group[] $groups
 */

use Yiisoft\Html\Html;

$this->setTitle($applicationParameters->getName());

?>

<div class="row">
    <div class="col-md-12">

        <h1>Groups</h1>
        <a href="<?= $urlGenerator->generate('group-add'); ?>" class="btn btn-success float-end">Create</a>
        <p class="lead">
            Overview of all groups
        </p>

        <br>
        <table class="table table-striped" data-search="true" data-toggle="table" data-pagination="true"
               data-page-size="100" data-sortable="true" data-click-to-select="true">
            <thead>
            <tr>
                <th data-checkbox="true"></th>
                <th scope="col">Name</th>
                <th scope="col">DN</th>
                <th scope="col">Members</th>
                <th>&nbsp;</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($groups as $group): ?>
                <?php
                $editUrl = $urlGenerator->generate('group-edit', ['dn' => $group->getDn()]);
                $membersUrl = $urlGenerator->generate('group-members', ['dn' => $group->getDn()]);
                ?>
                <tr>
                    <td data-checkbox="true"></td>
                    <td><?= $group->getTitle() ?? ''; ?></td>
                    <td><?= $group->getDn(); ?></td>
                    <td><?= count($group->getUserDns()); ?></td>
                    <td>
                        <?= Html::a('Edit', $editUrl, ['class' => 'btn btn-secondary btn-sm']); ?>
                        <?= Html::a('Members', $membersUrl, ['class' => 'btn btn-secondary btn-sm']); ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

    </div>
</div>
