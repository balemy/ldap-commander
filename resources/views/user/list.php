<?php

declare(strict_types=1);

/**
 * @var \Yiisoft\View\WebView $this
 * @var \Balemy\LdapCommander\ApplicationParameters $applicationParameters
 * @var \Yiisoft\Router\UrlGeneratorInterface $urlGenerator
 * @var \Yiisoft\Router\CurrentRoute $currentRoute
 * @var \Balemy\LdapCommander\Modules\UserManager\User[] $users
 * @var string[] $columns
 * @var string[] $organizationalUnits
 * @var string $organizationalUnit
 */

use Yiisoft\Html\Html;

$this->setTitle($applicationParameters->getName());
?>

<div class="row">
    <div class="col-md-12">
        <h1>Users </h1>

        <p class="lead">
            Overview of all users
        </p>
        <br/>

        <div>
            <a href="<?= $urlGenerator->generate('user-edit'); ?>" class="btn btn-success float-end">
                Create new user
            </a>

            <form class="form-floating" style="margin-right:150px">
                <?= Html::select('ou')
                    ->optionsData($organizationalUnits)
                    ->value($organizationalUnit)
                    ->addClass('form-select')
                    ->addAttributes(['style' => 'max-width:50%;', 'onchange' => 'this.form.submit()'])
                ?>
                <label for="ou">Organization Unit</label>
            </form>
        </div>

        <table class="table table-striped" data-search="true" data-toggle="table" data-pagination="true"
               data-page-size="100" data-sortable="true" data-click-to-select="true">
            <thead>
            <tr>
                <th data-checkbox="true"></th>
                <?php foreach ($columns as $label): ?>
                    <th scope="col"><?= Html::encode($label) ?></th>
                <?php endforeach; ?>
                <th>&nbsp;</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($users as $user): ?>
                <?php
                $editUrl = $urlGenerator->generate('user-edit', [], ['dn' => $user->getDn()]);
                ?>
                <tr>
                    <td data-checkbox="true"></td>
                    <?php foreach ($columns as $key => $label): ?>
                        <td>
                            <?php if ($key === 'mail' && $user->getFirstAttribute('mail') !== null): ?>
                                <?= Html::mailto($user->getFirstAttribute('mail')); ?>
                            <?php else: ?>
                                <?= Html::encode($user->getFirstAttribute($key)); ?>
                            <?php endif; ?>
                        </td>
                    <?php endforeach; ?>
                    <td style="width:150px">
                        <?= Html::a('Edit', $editUrl, ['class' => 'btn btn-secondary btn-sm']); ?>
                        <?= Html::a('Groups', $urlGenerator->generate('user-groups', [], ['dn' => $user->getDn()]), ['class' => 'btn btn-secondary btn-sm']); ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

    </div>
</div>

