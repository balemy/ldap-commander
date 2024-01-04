<?php

declare(strict_types=1);

use Yiisoft\Html\Html;

/**
 * @var Yiisoft\View\WebView $this
 * @var Balemy\LdapCommander\ApplicationParameters $applicationParameters
 * @var Yiisoft\Router\UrlGeneratorInterface $urlGenerator
 * @var Yiisoft\Router\CurrentRoute $currentRoute
 */

$this->setTitle('500');
?>

<div class="text-center">
    <h1>
        LDAP Config Connection Failed
    </h1>

    <p>
        Could not connect to OpenLDAP Dynamic Configuration.
    </p>

    <p class="alert alert-danger">
        <?= $message ?>
    </p>

    <p>
        Make sure that a correct `configDn` and `configPassword` are stored in the connection configuration <br> and that the OpenLDAP server uses the Dynamic Configuration.     </p>

    <p>
        <a href="<?= $urlGenerator->generate('home') ?>">Go Back Home</a>
    </p>
</div>
