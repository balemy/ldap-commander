<?php
declare(strict_types=1);

/**
 * @var \Yiisoft\View\WebView $this
 * @var \Yiisoft\Router\UrlGeneratorInterface $urlGenerator
 * @var \App\Ldap\Schema\AttributeType[] $attributes
 */

use Yiisoft\Html\Html;

?>
<table class="table table-striped" data-toggle="table" data-pagination="true" data-page-size="100">
    <thead>
    <tr>
        <th scope="col">Attribute</th>
        <th scope="col">Description</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($attributes as $attribute): ?>
        <tr>
            <th scope="row">
                <?= Html::a($attribute->getName(), $urlGenerator->generate('schema-attribute', ['oid' => $attribute->oid])) ?>
            </th>
            <td>
                <?= Html::encode($attribute->description); ?>
            </td>
        </tr>
    <?php endforeach; ?>
    <?php if (count($attributes) === 0): ?>
        <tr>
            <th colspan="2">No attributes defined.</th>
        </tr>
    <?php endif; ?>
    </tbody>
</table>
