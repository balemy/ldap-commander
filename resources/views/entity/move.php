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
use App\Widget\EntitySidebar;
use App\Widget\EntitySidebarLocation;
use App\Widget\RdnBreadcrumbs;
use Yiisoft\View\WebView;
use Yiisoft\Yii\View\Csrf;

$assetManager->register(EntityEditAsset::class);

$this->setTitle($applicationParameters->getName());
$this->registerJs('var ldapSchema=' . $schemaJsonInfo, WebView::POSITION_BEGIN);

?>

<div class="row">
    <div class="col-md-9">
        <?= RdnBreadcrumbs::widget(['$dn' => $dn]); ?>
        <h1> Move Entity </h1>
        <br>

        <div class="d-flex justify-content-center">
            <i class="fa-regular fa-face-grin" style="font-size:10em"></i>
        </div>
        <div class="d-flex justify-content-center">
            <br/>
            Implement me
        </div>

    </div>
    <div class="col-md-3">
        <?= EntitySidebar::widget(['$dn' => $dn, '$location' => EntitySidebarLocation::Edit]); ?>
    </div>
</div>