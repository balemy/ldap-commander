<?php

declare(strict_types=1);

/**
 * @var WebView $this
 * @var \Yiisoft\Assets\AssetManager $assetManager
 * @var \App\ApplicationParameters $applicationParameters
 * @var \Yiisoft\Router\UrlGeneratorInterface $urlGenerator
 * @var \App\Ldap\Schema\AttributeType[] $attributeTypes
 * @var \App\Ldap\EntityForm $entity
 * @var string $dn
 * @var Csrf $csrf
 * @var string $schemaJsonInfo
 * @var array $objectClassNames
 */

use App\Asset\EntityEditAsset;
use App\Fields\MultiPasswordField;
use App\Fields\MultiTextField;
use App\Fields\MultiFileField;
use App\Widget\EntitySidebar;
use App\Widget\EntitySidebarLocation;
use App\Widget\RdnBreadcrumbs;
use Yiisoft\Form\Field;
use Yiisoft\Html\Html;
use Yiisoft\Html\Tag\Label as LabelTag;
use Yiisoft\Html\Tag\Option;
use Yiisoft\View\WebView;
use Yiisoft\Yii\View\Csrf;

$assetManager->register(EntityEditAsset::class);

$this->setTitle($applicationParameters->getName());
$this->registerJs('var ldapSchema=' . $schemaJsonInfo, WebView::POSITION_BEGIN);

?>

<div class="row">
    <div class="col-md-9">
        <?= RdnBreadcrumbs::widget(['$dn' => $dn]); ?>
        <?php if ($entity->isNewRecord): ?>
            <h1> Add Entity </h1>
        <?php else: ?>
            <h1> Edit Entity </h1>
        <?php endif; ?>
        <br>

        <?= Html::form()
            ->post($urlGenerator->generate('entity-edit', ['dn' => $dn, 'new' => intval($entity->isNewRecord)]))
            ->enctype('multipart/form-data')
            ->csrf($csrf)
            ->open() ?>

        <div id="attribute-list">
            <?php foreach ($attributeTypes as $attribute => $attributeType): ?>
                <div class="attribute-row" data-attribute='<?= $attribute ?>'
                     data-attribute-label='<?= $entity->getAttributeLabel($attribute) ?>'>

                    <?php if ($attribute === 'objectclass'): ?>
                        <?= Field::getFactory('entity')->select($entity, 'objectclass')
                            ->optionsData($objectClassNames)
                            ->multiple(true) ?>
                    <?php elseif ($attribute === 'userpassword'): ?>
                        <?= Field::getFactory('entity')->input(MultiPasswordField::class, $entity, $attribute) ?>
                    <?php elseif ($entity->isBinaryAttribute($attribute)): ?>
                        <?= Field::getFactory('entity')->input(MultiFileField::class, $entity, $attribute,
                            ['$urlGenerator' => $urlGenerator, '$dn' => $entity->getDn()]) ?>
                    <?php else: ?>
                        <?= Field::getFactory('entity')->input(MultiTextField::class, $entity, $attribute) ?>
                    <?php endif; ?>

                </div>
            <?php endforeach; ?>
        </div>

        <hr>

        <div class="row mb-3">
            <?= LabelTag::tag()->addAttributes(['class' => 'col-sm-4 col-form-label'])->content('Add Attribute')->render() ?>

            <div class="col-sm-8 attribute-row-inputs">
                <select class="form-select" id="add-attribute-picker" data-placeholder="Choose attribute"></select>
            </div>

        </div>

        <hr>
        <?= Field::getFactory('entity')->select($entity, 'rdnAttribute')
            ->label('RDN Attribute')
            ->optionsData($attributeTypes)
            ->hint(($entity->isNewRecord) ? '' : 'Current DN: ' . $entity->getDn())
            ->multiple(false) ?>
        <hr>

        <?= Field::submitButton()
            ->buttonClass('btn btn-primary btn-lg mt-3')
            ->content('Submit') ?>

        <?= '</form>' ?>

    </div>
    <div class="col-md-3">
        <?= EntitySidebar::widget(['$dn' => $dn, '$location' => ($entity->isNewRecord) ? EntitySidebarLocation::Add : EntitySidebarLocation::Edit]); ?>
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
