<?php

declare(strict_types=1);

/**
 * @var \Yiisoft\View\WebView $this
 * @var \Balemy\LdapCommander\ApplicationParameters $applicationParameters
 * @var \Yiisoft\Router\UrlGeneratorInterface $urlGenerator
 * @var \Yiisoft\Router\CurrentRoute $currentRoute
 * @var \Balemy\LdapCommander\Modules\SlapdConfig\Models\BindUser[] $bindUsers
 */

use Yiisoft\Html\Html;

$this->setTitle($applicationParameters->getName());

?>

<div class="row">
    <div class="col-md-12">

        <h1>Module Configurations</h1>
        <p class="lead">
            Tbd
        </p>

        <br>
        <ul>
            <li><a href="<?= $urlGenerator->generate('module-config-memberof') ?>">MemberOf Overlay</a></li>
        </ul>


    </div>
</div>
