<?php

declare(strict_types=1);

/**
 * @var \Yiisoft\View\WebView $this
 * @var \App\ApplicationParameters $applicationParameters
 * @var \Yiisoft\Router\UrlGeneratorInterface $urlGenerator
 * @var \Yiisoft\Router\CurrentRoute $currentRoute
 * @var array $results
 */

use App\Helper\OID;
use Yiisoft\Html\Html;

$this->setTitle($applicationParameters->getName());
?>


<div class="row">
    <div class="col-md-9">
        <h1>Server Information</h1>
        <br>
        <table class="table table-striped">
            <thead>
            <tr>
                <th scope="col">RDN</th>
                <th scope="col">ObjectClass(es)</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($results as $key => $values): ?>
                <tr>
                    <td>
                        <?= Html::encode($key) ?>
                    </td>
                    <td>
                        <ul>
                            <?php foreach ($values as $value): ?>
                                <?php if (OID::hasDescription($value)): ?>
                                    <li>
                                        <?= Html::encode(OID::getsDescription($value)) ?><br />
                                        <small><?= Html::encode($value) ?></small>
                                    </li>
                                <?php else: ?>
                                    <li><?= Html::encode($value) ?></li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </ul>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

    </div>
</div>
