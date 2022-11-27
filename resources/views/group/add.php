<?php

declare(strict_types=1);

/**
 * @var \Yiisoft\View\WebView $this
 * @var \Balemy\LdapCommander\ApplicationParameters $applicationParameters
 * @var \Yiisoft\Router\UrlGeneratorInterface $urlGenerator
 * @var \Yiisoft\Router\CurrentRoute $currentRoute
 * @var Csrf $csrf
 * @var string $dn
 * @var string[] $users
 * @var string[] $parentDns
 * @var \Balemy\LdapCommander\Ldap\GroupAddForm $formModel
 */

use Yiisoft\Form\Field;
use Yiisoft\Html\Html;
use Yiisoft\Html\Tag\Form;
use Yiisoft\Yii\View\Csrf;

$this->setTitle($applicationParameters->getName());
?>

<div class="row">
    <div class="col-md-9">
        <h1>Add New Group</h1>
        <p class="lead">
            Creates a new group.
        </p>
        <br>

        <?= Html::form()->post($urlGenerator->generate('group-add'))->csrf($csrf)->open() ?>


        <?= Field::text($formModel, 'title')
            ->autofocus()
            ->tabindex(1) ?>

        <?= Field::textarea($formModel, 'description')
            ->addInputAttributes(['style' => 'height:150px'])
            ->tabindex(2) ?>


        <?= Field::select($formModel, 'parentDn')
            ->optionsData($parentDns)
            ->tabindex(3) ?>

        <?= Field::select($formModel, 'initialMembers[]')
            ->multiple(true)
            ->optionsData($users)
            ->tabindex(4) ?>

        <?= Field::submitButton()
            ->tabindex(5)
            ->content('Save') ?>

        <?= Form::tag()->close(); ?>
    </div>

    <div class="col-md-3">
    </div>

</div>
<script>
    $(document).ready(function () {
        $('#groupaddform-parentdn').select2({
            theme: 'bootstrap-5',
            placeholder: "Parent DN",
            multiple: false
        });

        $('#groupaddform-initialmembers').select2({
            theme: 'bootstrap-5',
            placeholder: "Initial group members",
            multiple: true
        });
    });
</script>
