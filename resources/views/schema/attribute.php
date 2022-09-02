<?php

declare(strict_types=1);

/**
 * @var \Yiisoft\View\WebView $this
 * @var \App\ApplicationParameters $applicationParameters
 * @var \Yiisoft\Router\UrlGeneratorInterface $urlGenerator
 * @var \Yiisoft\Router\CurrentRoute $currentRoute
 * @var \App\Ldap\Schema\AttributeType $attribute
 * @var \App\Ldap\Schema\ObjectClass[] $objectClasses
 */

use Yiisoft\Html\Html;

$this->setTitle($applicationParameters->getName());
?>

<div class="row">
    <div class="col-md-9">
        <h1> Attribute: <?= Html::encode($attribute->getName()); ?> </h1>
        <p class="lead">
            <?= Html::encode($attribute->description); ?>
        </p>

        <br>
        <h2>Details</h2>
        <table class="table table-striped">
            <tbody>
            <tr>
                <th scope="row">OID</th>
                <td><?= $attribute->oid ?></td>
            </tr>
            <?php if (!empty($attribute->substr)): ?>
                <tr>
                    <th scope="row">SubStr</th>
                    <td><?= $attribute->substr ?></td>
                </tr>
            <?php endif; ?>
            <?php if (!empty($attribute->equality)): ?>
            <tr>
                <th scope="row">Equality</th>
                <td><?= $attribute->equality ?></td>
            </tr>
            <?php endif; ?>
            <?php if (!empty($attribute->usage)): ?>
            <tr>
                <th scope="row">Usage</th>
                <td><?= $attribute->usage ?></td>
            </tr>
            <?php endif; ?>
            <?php if (!empty($attribute->syntax)): ?>
            <tr>
                <th scope="row">Syntax</th>
                <td><?= $attribute->syntax ?></td>
            </tr>
            <?php endif; ?>
            <tr>
                <th scope="row">Is single Value</th>
                <td><?= ($attribute->isSingleValue) ? 'Yes' : 'No' ?></td>
            </tr>
            <tr>
                <th scope="row">Usages</th>
                <td><?= count($objectClasses) ?></td>
            </tr>
        </table>

        <br>
        <h2>Object Classes</h2>
        <?= $this->render('_objectclasses', ['objectClasses' => $objectClasses, 'urlGenerator' => $urlGenerator]); ?>


    </div>
    <div class="col-md-3">
        <ul class="list-group">
            <li class="list-group-item"><a href="<?= $urlGenerator->generate('schema') ?>">Back to overview</li>
        </ul>
    </div>
</div>
