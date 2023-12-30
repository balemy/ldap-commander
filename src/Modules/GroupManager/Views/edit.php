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
 * @var string[] $users
 * @var \Balemy\LdapCommander\Modules\GroupManager\GroupForm $groupModel
 */

use Balemy\LdapCommander\Modules\GroupManager\SidebarWidget;
use Balemy\LdapCommander\Modules\GroupManager\GroupSidebarLocation;
use Yiisoft\FormModel\Field;
use Yiisoft\Html\Html;
use Yiisoft\Html\Tag\Form;
use Yiisoft\Yii\View\Csrf;

$this->setTitle($applicationParameters->getName());
?>

<div class="row">
    <div class="col-md-9">
        <?php if (!$groupModel->isNewRecord): ?>
            <h1>Edit Group</h1>
            <table class="table">
                <tr>
                    <td>DN</td>
                    <td>
                        <code><?= Html::encode($groupModel->getDn()) ?></code>
                    </td>
                </tr>
                <tr>
                    <td>Login filter</td>
                    <td>
                        <code>(&(uid=%s)(objectClass=inetOrgPerson)(memberOf=<?= Html::encode($groupModel->getDn()) ?>))</code>
                    </td>
                </tr>
                <tr>
                    <td>Search filter</td>
                    <td>
                        <code>(&(objectClass=inetOrgPerson)(memberof=<?= Html::encode($groupModel->getDn()) ?>))</code>
                    </td>
                </tr>

            </table>

        <?php else: ?>
            <h1>Create Group</h1>
            <p class="lead">
            </p>
        <?php endif; ?>
        <br>

        <?= Html::form()->post($urlGenerator->generate('group-edit', [], ['dn' => $dn]))->csrf($csrf)->open() ?>

        <?= Field::text($groupModel, 'cn')
            ->autofocus()
            ->tabindex(1) ?>

        <?= Field::textarea($groupModel, 'description')
            ->addInputAttributes(['style' => 'height:150px'])
            ->tabindex(2) ?>

        <?= Field::select($groupModel, 'parentDn')
            ->optionsData($parentDNs)
            ->tabindex(3) ?>

        <?php if ($groupModel->isNewRecord): ?>
            <?= Field::select($groupModel, 'uniqueMember')
                ->multiple(true)
                ->optionsData($users)
                ->tabindex(4) ?>
        <?php endif; ?>

        <?= Field::submitButton()
            ->tabindex(3)
            ->content('Save') ?>

        <?= Form::tag()->close(); ?>
    </div>

    <div class="col-md-3">
        <?= SidebarWidget::widget([], ['$dn' => $dn, '$location' => GroupSidebarLocation::Edit]); ?>
    </div>

</div>

<script>
    $(document).ready(function () {
        $('#groupform-uniquemember').select2({
            theme: 'bootstrap-5',
            placeholder: "Initial group members",
            multiple: true
        });
    });
</script>

