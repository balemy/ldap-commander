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

        <h1>Modules</h1>
        <p class="lead">
            A list of modules loaded by OpenLDAP. An extended configuration is available for some modules.
        </p>

        <ul>

            <?php foreach ($modules as $module): ?>
                <li>
                    <?php if (str_contains($module, 'memberof')): ?>
                        <a href="<?= $urlGenerator->generate('module-config-memberof') ?>"><?= $module ?></a>
                    <?php else: ?>
                        <?= $module ?>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>


    </div>
</div>
