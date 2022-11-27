<?php

declare(strict_types=1);

/**
 * @var Balemy\LdapCommander\ApplicationParameters $applicationParameters
 */

use Balemy\LdapCommander\Widget\PerformanceMetrics;

?>
<footer class="footer mt-auto py-3 text-muted">
    <div class="container text-center">
        <br>
        <br>
        <p>
            LDAP Commander<br />
            <small>Version <?= $applicationParameters->getVersion() ?>
                <br>
                <br>
                <?= PerformanceMetrics::widget() ?></small>
        </p>
    </div>
</footer>

