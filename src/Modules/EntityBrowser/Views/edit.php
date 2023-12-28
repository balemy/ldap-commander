<?php

declare(strict_types=1);

/**
 * @var WebView $this
 * @var \Yiisoft\Assets\AssetManager $assetManager
 * @var \Balemy\LdapCommander\ApplicationParameters $applicationParameters
 * @var \Yiisoft\Router\UrlGeneratorInterface $urlGenerator
 * @var \Balemy\LdapCommander\Schema\AttributeType[] $attributeTypes
 * @var \Balemy\LdapCommander\Ldap\EntityForm $entity
 * @var string $dn
 * @var Csrf $csrf
 * @var string $schemaJsonInfo
 * @var array $objectClassNames
 */

use Balemy\LdapCommander\Modules\EntityBrowser\Assets\EntityEditAsset;
use Balemy\LdapCommander\Modules\EntityBrowser\Fields\MultiFileField;
use Balemy\LdapCommander\Modules\EntityBrowser\Fields\MultiPasswordField;
use Balemy\LdapCommander\Modules\EntityBrowser\Fields\MultiTextField;
use Balemy\LdapCommander\Modules\EntityBrowser\Widgets\EntitySidebar;
use Balemy\LdapCommander\Modules\EntityBrowser\Widgets\EntitySidebarLocation;
use Balemy\LdapCommander\Modules\EntityBrowser\Widgets\RdnBreadcrumbs;
use Yiisoft\FormModel\Field;
use Yiisoft\FormModel\FormModelInputData;
use Yiisoft\Html\Html;
use Yiisoft\Html\Tag\Label as LabelTag;
use Yiisoft\View\WebView;
use Yiisoft\Yii\View\Csrf;

$assetManager->register(EntityEditAsset::class);

$this->setTitle($applicationParameters->getName());
$this->registerJs('var ldapSchema=' . $schemaJsonInfo, WebView::POSITION_BEGIN);

?>

<div class="row">
    <div class="col-md-9">
        <?= RdnBreadcrumbs::widget([], ['$dn' => $dn]); ?>
        <?php if ($entity->isNewRecord): ?>
            <h1> Add Entity </h1>
        <?php else: ?>
            <h1> Edit Entity </h1>
            <p class="lead">
                <?= Html::encode($dn); ?>
            </p>
        <?php endif; ?>
        <br>

        <?= Html::form()
            ->post($urlGenerator->generate('entity-edit', [], ['dn' => $dn, 'new' => intval($entity->isNewRecord)]))
            ->id('entityForm')
            ->enctype('multipart/form-data')
            ->csrf($csrf)
            ->open() ?>

        <div class="d-flex justify-content-center" id="attribute-list-loader">
            <div class="spinner-border text-primary" style="width: 15rem; height: 15rem;" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>

        <div id="attribute-list" style="display:none">
            <?php foreach ($attributeTypes as $attribute => $attributeType): ?>
                <div class="attribute-row" data-attribute='<?= $attribute ?>'
                     data-attribute-label='<?= $entity->getPropertyLabel($attribute) ?>'>

                    <?php if ($attribute === 'objectclass'): ?>
                        <?= Field::select($entity, 'objectclass', theme: 'label-left')->optionsData($objectClassNames)->multiple(true); ?>
                    <?php elseif ($attribute === 'userpassword'): ?>
                        <?= MultiPasswordField::widget(config: ['$entityForm' => $entity], theme: 'label-left')->inputData(new FormModelInputData($entity, $attribute)) ?>
                    <?php elseif ($entity->isBinaryAttribute($attribute)): ?>
                        <?= MultiFileField::widget(config: ['$dn' => $entity->getDn(), '$entityForm' => $entity], theme: 'label-left')->inputData(new FormModelInputData($entity, $attribute)) ?>
                    <?php else: ?>
                        <?= MultiTextField::widget(config: ['$entityForm' => $entity], theme: 'label-left')->inputData(new FormModelInputData($entity, $attribute)) ?>
                    <?php endif; ?>

                </div>
            <?php endforeach; ?>
        </div>

        <div id="attribute-list-bottom" style="display:none">
            <hr>

            <div class="row mb-3">
                <?= LabelTag::tag()->addAttributes(['class' => 'col-sm-4 col-form-label'])->content('Add Attribute')->render() ?>

                <div class="col-sm-8 attribute-row-inputs">
                    <select class="form-select" id="add-attribute-picker" data-placeholder="Choose attribute"></select>
                </div>

            </div>

            <?php if ($entity->isNewRecord): ?>
                <hr>
                <?= Field::select($entity, 'rdnAttribute', theme: 'label-left')
                    ->label('RDN Attribute')
                    ->optionsData($attributeTypes)->multiple(true)
                    ->hint(($entity->isNewRecord) ? '' : 'Current DN: ' . $entity->getDn())
                    ->multiple(false) ?>
            <?php endif; ?>
            <hr>

            <?= Field::submitButton()
                ->buttonClass('btn btn-primary btn-lg mt-3')
                ->content('Submit') ?>

        </div>
        <?= '</form>' ?>

    </div>
    <div class="col-md-3">
        <?= EntitySidebar::widget([], ['$dn' => $dn, '$location' => ($entity->isNewRecord) ? EntitySidebarLocation::Add : EntitySidebarLocation::Edit]); ?>
    </div>
</div>


<!-- Modal -->
<div class="modal fade" id="staticSetPassword" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Set New Password</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Passwords will be hashed using SHA256.
                <br/>
                <br/>

                <div class="form-floating">
                    <input type="password" class="form-control" id="new-password-input" placeholder="New Password">
                    <label for="floatingPassword">New Password</label>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="setPassButton">Set Password</button>
            </div>
        </div>
    </div>
</div>
