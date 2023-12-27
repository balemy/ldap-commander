<?php

declare(strict_types=1);

/**
 * @var \Yiisoft\View\WebView $this
 * @var \Balemy\LdapCommander\ApplicationParameters $applicationParameters
 * @var \Yiisoft\Router\UrlGeneratorInterface $urlGenerator
 * @var \Yiisoft\Router\CurrentRoute $currentRoute
 * @var Csrf $csrf
 * @var string $dn
 * @var string[] $parentDns
 * @var \Balemy\LdapCommander\Modules\ServerConfig\MemberOf\MemberOfForm $formModel
 */

use Yiisoft\Yii\View\Csrf;

$this->setTitle($applicationParameters->getName());
?>

<div class="row">
    <div class="col-md-9">
        <h1>Schema Configurations</h1>
        <p class="lead">

        </p>
        <br>

    </div>

    <div class="col-md-3">
    </div>

</div>
