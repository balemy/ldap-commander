<?php

declare(strict_types=1);

/**
 * @var \Yiisoft\View\WebView $this
 * @var \Balemy\LdapCommander\ApplicationParameters $applicationParameters
 * @var \Yiisoft\Router\UrlGeneratorInterface $urlGenerator
 * @var \Yiisoft\Router\CurrentRoute $currentRoute
 * @var \Balemy\LdapCommander\Ldap\Schema\ObjectClass[] $objectClasses
 */

use Yiisoft\Html\Html;

$this->setTitle($applicationParameters->getName());
?>

<div class="row">
    <div class="col-md-12">
        <h1> Object Classes </h1>
        <br>
        <?= $this->render('_objectclasses', ['objectClasses' => $objectClasses, 'urlGenerator' => $urlGenerator]); ?>

    </div>
</div>
