<?php

declare(strict_types=1);

/**
 * @var \Yiisoft\View\WebView $this
 * @var \Balemy\LdapCommander\ApplicationParameters $applicationParameters
 * @var \Yiisoft\Router\UrlGeneratorInterface $urlGenerator
 * @var \Yiisoft\Router\CurrentRoute $currentRoute
 * @var array $results
 * @var string $dn
 */

use Balemy\LdapCommander\Widget\EntitySidebar;
use Balemy\LdapCommander\Widget\EntitySidebarLocation;
use Yiisoft\Html\Html;

$this->setTitle($applicationParameters->getName());
?>

<div class="row">
    <div class="col-md-9">
        <?= \Balemy\LdapCommander\Widget\RdnBreadcrumbs::widget(['$dn' => $dn]); ?>

        <h1> List Children </h1>
        <p class="lead">
            <?= Html::encode($dn); ?>
        </p>
        <br>
        <table class="table table-striped" data-search="true" data-toggle="table" data-pagination="true" data-page-size="100">
            <thead>
            <tr>
                <th scope="col">RDN</th>
                <th scope="col">ObjectClass(es)</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($results as $result): ?>
                <?php
                array_shift($result['objectclass']);
                $title = $result['dn'];
                $title = str_replace($dn, '<small>' . $dn . '</small>', $title);
                ?>
                <tr>
                    <th scope="row"><?= Html::a($title, $urlGenerator->generate('entity', ['dn' => $result['dn']]))->encode(false) ?></th>
                    <td><?= implode("<br>", $result['objectclass']) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

    </div>
    <div class="col-md-3">
        <?= EntitySidebar::widget(['$dn' => $dn, '$location' => EntitySidebarLocation::ListChildren]); ?>
    </div>
</div>

