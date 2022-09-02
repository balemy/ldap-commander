<?php
declare(strict_types=1);

/**
 * @var \Yiisoft\View\WebView $this
 * @var \Yiisoft\Router\UrlGeneratorInterface $urlGenerator
 * @var \App\Ldap\Schema\ObjectClass[] $objectClasses
 */

use Yiisoft\Html\Html;

?>
<table class="table table-striped">
    <thead>
    <tr>
        <th scope="col">Object Class</th>
        <th scope="col">Description</th>
        <th scope="col">Attributes</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($objectClasses as $objectClass): ?>
        <tr>
            <th scope="row">
                <?= Html::a($objectClass->name,
                    $urlGenerator->generate('schema-objectclass', ['oid' => $objectClass->oid])
                ) ?>
            </th>
            <td>
                <?= Html::encode($objectClass->description); ?>
            </td>
            <td>
                <?= count($objectClass->mayAttributes) + count($objectClass->mustAttributes); ?>
            </td>
        </tr>
    <?php endforeach; ?>
    <?php if (count($objectClasses) === 0): ?>
        <tr>
            <th colspan="3">No object classes found.</th>
        </tr>
    <?php endif; ?>
    </tbody>
</table>
