<?php

declare(strict_types=1);

/**
 * @var \Yiisoft\View\WebView $this
 * @var \App\ApplicationParameters $applicationParameters
 * @var \Yiisoft\Router\UrlGeneratorInterface $urlGenerator
 * @var \Yiisoft\Router\CurrentRoute $currentRoute
 * @var \App\Ldap\User[] $users
 */

use Yiisoft\Html\Html;

$this->setTitle($applicationParameters->getName());
?>

<div class="row">
    <div class="col-md-12">
        <h1> Users </h1>
        <p class="lead">
            Overview of all users
        </p>
        <br>
        <table class="table table-striped" data-search="true" data-toggle="table" data-pagination="true"
               data-page-size="100" data-sortable="true" data-click-to-select="true">
            <thead>
            <tr>
                <th data-checkbox="true"></th>
                <th scope="col">ID</th>
                <th scope="col">Username</th>
                <th scope="col">First name</th>
                <th scope="col">Last name</th>
                <th scope="col">E-Mail</th>
                <th>&nbsp;</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($users as $user): ?>
                <?php
                $editUrl = $urlGenerator->generate('entity-edit', ['dn' => $user->getDn()]);
                ?>
                <tr>
                    <td data-checkbox="true"></td>
                    <td><?= ($user->getId()) ? Html::a($user->getId(), $editUrl) : ''; ?></td>
                    <td><?= $user->getUsername() ?? ''; ?></td>
                    <td><?= $user->getFirstName() ?? ''; ?></td>
                    <td><?= $user->getLastName(); ?></td>
                    <td><?= ($user->getMail() !== null) ? Html::mailto($user->getMail(), $user->getMail()) : '' ?></td>
                    <td><?= Html::a('Edit', $editUrl, ['class' => 'btn btn-secondary btn-sm']); ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

    </div>
</div>

