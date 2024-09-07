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
use Yiisoft\Yii\View\Renderer\Csrf;

$assetManager->register(\Balemy\LdapCommander\Asset\MultiValueFormFieldAsset::class);

$this->setTitle($applicationParameters->getName());
?>

<div class="row">
    <div class="col-md-9">
        <h1>Access Control</h1>
        <p class="lead">
            As the directory gets populated with more and more data of varying sensitivity, controlling the kinds of access granted to the directory becomes more and more critical.        </p>
        <br>

        <?= Html::form()->post($urlGenerator->generate('access-control'))->csrf($csrf)->open() ?>

        <div class="row">

            <div class="col-sm-12">
                <?= MultiTextAreaField::widget(config: [], theme: 'label-left')
                    ->inputData(new FormModelInputData($model, 'olcAccess'))
                    ->label('Access Rules')
                ?>
            </div>
        </div>

        <?= Field::submitButton()
            ->tabindex(3)
            ->content('Save') ?>

        <?= Form::tag()->close(); ?>
    </div>
</div>

