<?php

declare(strict_types=1);

/**
 * @var \Yiisoft\View\WebView $this
 * @var \Balemy\LdapCommander\ApplicationParameters $applicationParameters
 * @var \Yiisoft\Router\UrlGeneratorInterface $urlGenerator
 * @var \Yiisoft\Router\CurrentRoute $currentRoute
 * @var Csrf $csrf
 * @var \Balemy\LdapCommander\Modules\RawQuery\QueryForm $queryForm
 * @var string[] $results
 */

use Yiisoft\FormModel\Field;
use Yiisoft\Html\Html;
use Yiisoft\Html\Tag\Form;
use Yiisoft\Yii\View\Csrf;

$this->setTitle($applicationParameters->getName());
?>

<div class="row">
    <div class="col-md-9">
        <h1>Execute LDAP Query</h1>
        <p class="lead">
        </p>

        <br>

        <?= Html::form()->post($urlGenerator->generate('raw-query'))->csrf($csrf)->open() ?>

        <div class="row">
            <div class="col-sm-12">
                <?= Field::textarea($queryForm, 'query') ?>
            </div>
        </div>
        <?= Field::submitButton()
            ->tabindex(3)
            ->content('Execute') ?>

        <?= Form::tag()->close(); ?>


        <?php if (!empty($results)): ?>
            <br>
            <br>
            <table class="table">
                <tr>
                    <th>DN</th>
                    <th>Object Class</th>
                </tr>

                <?php foreach ($results as $result): ?>
                    <?php
                    array_shift($result['objectclass']);
                    ?>
                    <tr>
                        <td>
                            <?= Html::a(Html::encode($result['dn']), $urlGenerator->generate('entity-edit', [], ['dn' => $result['dn']])) ?>
                        </td>
                        <td><?= implode(', ', $result['objectclass']) ?></td>
                    </tr>
                <?php endforeach; ?>

            </table>


        <?php endif; ?>

    </div>


</div>

