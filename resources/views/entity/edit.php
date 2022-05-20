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
 * @var string $schemaJsonInfo
 * @var array $objectClassNames
 */

use App\Asset\EntityEditAsset;
use App\TextListWidget;
use App\Widget\EntitySidebar;
use App\Widget\EntitySidebarLocation;
use App\Widget\RdnBreadcrumbs;
use Yiisoft\Form\Helper\HtmlForm;
use Yiisoft\Form\Widget\Field;
use Yiisoft\Form\Widget\FieldPart\Label;
use Yiisoft\Form\Widget\Form;
use Yiisoft\Form\Widget\Select;
use Yiisoft\Html\Tag\Label as LabelTag;
use Yiisoft\View\WebView;

$assetManager->register(EntityEditAsset::class);

$this->setTitle($applicationParameters->getName());


$entityObjectClasses = [];
foreach ($entity->getAttributeValueAsArray('objectclass') as $oc) {
    $entityObjectClasses[strtolower($oc)] = $oc;
}

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

        <?= Form::widget()
            ->action($urlGenerator->generate('entity-edit', ['dn' => $dn, 'new' => intval($entity->isNewRecord)]))
            ->begin() ?>

        <div class="row mb-3">
            <?= LabelTag::tag()->attributes(['class' => 'col-sm-4 col-form-label'])->content('RDN Attribute')->render() ?>

            <div class="col-sm-7 attribute-row-inputs">
                <?= Select::widget()->for($entity, 'rdnAttribute')
                    ->for($entity, 'rdnAttribute')
                    ->value($entity->getAttributeValue('rdnAttribute'))
                    ->optionsData($attributeTypes)
                    ->attributes([
                        'class' => 'form-select',
                        'data-placeholer' => 'Choose RDN attribute',
                    ])
                ?>
                <?php if (!$entity->isNewRecord): ?>
                    <small>Current DN: <?= $entity->getDn() ?></small>
                <?php endif; ?>
            </div>

        </div>

        <div id="attributeList">
            <?php foreach ($attributeTypes as $attribute => $attributeType): ?>
                <div class="row mb-3 attribute-row"
                     data-attribute='<?= $attribute ?>'
                     data-attribute-label='<?= $entity->getAttributeLabel($attribute) ?>'>

                    <?= Label::widget()
                        ->attributes(['class' => 'col-sm-4 col-form-label'])
                        ->for($entity, $attribute)
                        ->render(); ?>

                    <?php if ($attribute === 'objectclass'): ?>
                        <div class="col-sm-7">
                            <?= Select::widget()->for($entity, 'objectclass')
                                ->items($objectClassNames)
                                ->value(array_keys($entityObjectClasses))
                                ->multiple(true)
                                ->attributes(['class' => 'form-select']);
                            ?>
                        </div>
                    <?php else: ?>
                        <div class="col-sm-7 attribute-row-inputs">
                            <?php
                            $values = $entity->getAttributeValueAsArray($attribute);

                            // Make sure to have at least on empty value to draw input obx
                            if (empty($values)) {
                                $values[0] = '';
                            }
                            ?>

                            <?php foreach ($values as $i => $val): ?>
                                <?= TextListWidget::widget()->for($entity, $attribute . '[' . $i . ']')->attributes(['value' => $val]) ?>
                            <?php endforeach; ?>
                        </div>

                        <div class="col-sm-1">
                            <?php if ($entity->isMultiValueAttribute($attribute)): ?>
                                <a class="btn btn-light add-input"
                                   data-input-id="<?= HtmlForm::getInputId($entity, $attribute . '[replace-with-id]') ?>"
                                   data-input-name="<?= HtmlForm::getInputName($entity, $attribute . '[replace-with-id]') ?>"
                                >+</a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>

        <hr>

        <div class="row mb-3">
            <?= LabelTag::tag()->attributes(['class' => 'col-sm-4 col-form-label'])->content()->render() ?>

            <div class="col-sm-7 attribute-row-inputs">
                <select class="form-select" id="add-attribute-picker" data-placeholder="Choose attribute"></select>
            </div>

        </div>

        <?= Field::widget()
            ->class('btn btn-primary btn-lg mt-3')
            ->containerClass('d-grid gap-2 form-floating')
            ->id('login-button')
            ->submitButton()
            ->tabindex(3)
            ->value("Submit")
        ?>
        <?= Form::end() ?>
    </div>
    <div class="col-md-3">
        <?= EntitySidebar::widget(['$dn' => $dn, '$location' => ($entity->isNewRecord) ? EntitySidebarLocation::Add : EntitySidebarLocation::Edit]); ?>
    </div>
</div>
