<?php

declare(strict_types=1);

/**
 * @var \Yiisoft\View\WebView $this
 * @var \Balemy\LdapCommander\ApplicationParameters $applicationParameters
 * @var \Yiisoft\Router\UrlGeneratorInterface $urlGenerator
 * @var \Yiisoft\Router\CurrentRoute $currentRoute
 * @var \Balemy\LdapCommander\Modules\SlapdConfig\Models\BindUser[] $bindUsers
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

        <h1>Bind Users</h1>
        <a href="<?= $urlGenerator->generate('bind-user-edit'); ?>" class="btn btn-success float-end">Create new bind
            user</a>
        <p class="lead">
            User accounts that are able to connect to the LDAP server. This is required for applications using LDAP
            integration.
        </p>

        <br>
        <table class="table table-striped" data-search="true" data-toggle="table" data-pagination="true"
               data-page-size="100" data-sortable="true" data-click-to-select="true">
            <thead>
            <tr>
                <th data-checkbox="true"></th>
                <th class="fit" scope="col">Username (CN)</th>
                <th scope="col">Description</th>
                <th>&nbsp;</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($bindUsers as $bindUser): ?>
                <?php
                $editUrl = $urlGenerator->generate('bind-user-edit', [], ['dn' => $bindUser->getDn()]);
                ?>
                <tr>
                    <td data-checkbox="true"></td>
                    <td class="fit"><?= $bindUser->getPropertyValue('cn') ?? ''; ?></td>
                    <td><?= Html::encode($bindUser->getPropertyValue('description')); ?></td>
                    <td style="width:150px">
                        <?= Html::a('Edit', $editUrl, ['class' => 'btn btn-secondary btn-sm']); ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

    </div>
</div>
