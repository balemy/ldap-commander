<?php

declare(strict_types=1);

/**
 * @var \Yiisoft\View\WebView $this
 * @var \Balemy\LdapCommander\ApplicationParameters $applicationParameters
 * @var \Yiisoft\Router\UrlGeneratorInterface $urlGenerator
 * @var \Yiisoft\Router\CurrentRoute $currentRoute
 * @var \Balemy\LdapCommander\Ldap\Schema\ObjectClass $objectClass
 */

use Yiisoft\Html\Html;

$this->setTitle($applicationParameters->getName());
?>

<div class="row">
    <div class="col-md-9">
        <h1> Object Class: <?= Html::encode($objectClass->name); ?> </h1>
        <p class="lead">
            <?= Html::encode($objectClass->description); ?>
        </p>

        <br>
        <h2> Super Classes</h2>
        <?= $this->render('_objectclasses', ['objectClasses' => $objectClass->getSuperClasses(), 'urlGenerator' => $urlGenerator]); ?>

        <br>
        <h2> Must Attributes</h2>
        <?= $this->render('_attributeList', ['attributes' => $objectClass->getMustAttributes(), 'urlGenerator' => $urlGenerator]); ?>

        <br>
        <h2> May Attributes</h2>
        <?= $this->render('_attributeList', ['attributes' => $objectClass->getMayAttributes(), 'urlGenerator' => $urlGenerator]); ?>

        <br>
        <h2> Definition</h2>
        <div class="alert alert-light" role="alert">
            <?= Html::encode($objectClass->definition); ?>
        </div>

    </div>
    <div class="col-md-3">
        <ul class="list-group">
            <li class="list-group-item"><a href="<?= $urlGenerator->generate('schema') ?>">Back to overview</a></li>
        </ul>
    </div>
</div>
