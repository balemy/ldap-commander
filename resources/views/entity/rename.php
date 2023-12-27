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
use Balemy\LdapCommander\Modules\EntityBrowser\Widgets\RdnBreadcrumbs;
use Balemy\LdapCommander\Widget\EntitySidebar;
use Balemy\LdapCommander\Widget\EntitySidebarLocation;
use Yiisoft\View\WebView;
use Yiisoft\Yii\View\Csrf;

$assetManager->register(EntityEditAsset::class);

$this->setTitle($applicationParameters->getName());
$this->registerJs('var ldapSchema=' . $schemaJsonInfo, WebView::POSITION_BEGIN);

?>

<div class="row">
    <div class="col-md-9">
        <?= RdnBreadcrumbs::widget([], ['$dn' => $dn]); ?>
        <h1> Rename Entity </h1>
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
        <?= EntitySidebar::widget([], ['$dn' => $dn, '$location' => EntitySidebarLocation::Edit]); ?>
    </div>
</div>
