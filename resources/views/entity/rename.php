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
        <h1> Rename Entity </h1>
        <br>

        TBD

    </div>
    <div class="col-md-3">
        <?= EntitySidebar::widget(['$dn' => $dn, '$location' => EntitySidebarLocation::Edit]); ?>
    </div>
</div>
