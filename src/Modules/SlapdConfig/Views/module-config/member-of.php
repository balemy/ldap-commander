<?php

declare(strict_types=1);

/**
 * @var \Yiisoft\View\WebView $this
 * @var \Balemy\LdapCommander\ApplicationParameters $applicationParameters
 * @var \Yiisoft\Router\UrlGeneratorInterface $urlGenerator
 * @var \Yiisoft\Router\CurrentRoute $currentRoute
 * @var \Yiisoft\Assets\AssetManager $assetManager
 * @var Csrf $csrf
 * @var \Balemy\LdapCommander\Modules\SlapdConfig\Models\AccessControl $model
 */

use Balemy\LdapCommander\Form\Fields\MultiTextAreaField;
use Yiisoft\FormModel\Field;
use Yiisoft\FormModel\FormModelInputData;
use Yiisoft\Html\Html;
use Yiisoft\Html\Tag\Form;
use Yiisoft\Yii\View\Csrf;

$assetManager->register(\Balemy\LdapCommander\Asset\MultiValueFormFieldAsset::class);

$this->setTitle($applicationParameters->getName());
?>

<div class="row">
    <div class="col-md-9">
        <h1>MemberOf Overlay</h1>
        <p class="lead">
            Reverse Group Membership Maintenance
        </p>
        <br>

        <?= Html::form()->post()->csrf($csrf)->open() ?>

        <div class="row">

            <div class="col-sm-12">
                <?= Field::text($model, 'olcMemberOfGroupOC') ?>
                <?= Field::text($model, 'olcMemberOfMemberAD') ?>
                <?= Field::text($model, 'olcMemberOfMemberOfAD') ?>
                <?= Field::text($model, 'olcMemberOfDangling') ?>
                <?= Field::text($model, 'olcMemberOfRefInt') ?>
            </div>
        </div>

        <?= Field::submitButton()
            ->tabindex(3)
            ->content('Save') ?>

        <?= Form::tag()->close(); ?>
    </div>
</div>

