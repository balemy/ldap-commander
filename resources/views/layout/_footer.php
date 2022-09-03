<?php

declare(strict_types=1);

/**
 * @var App\ApplicationParameters $applicationParameters
 */

use App\Widget\PerformanceMetrics;

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

