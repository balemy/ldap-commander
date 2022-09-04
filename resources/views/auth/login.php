<?php

declare(strict_types=1);

use App\Ldap\ConnectionDetails;
use Yiisoft\Form\Field;
use Yiisoft\Html\Tag\Form;
use Yiisoft\Html\Tag\Select;

/**
 * @var \Yiisoft\View\WebView $this
 * @var \Yiisoft\Router\UrlGeneratorInterface $urlGenerator
 * @var string $csrf
 * @var int $connectionId
 * @var \App\Ldap\LoginForm $formModel
 */

$error = $error ?? null;
?>

<div class="container py-5 h-100">
    <div class="row d-flex justify-content-center align-items-center h-100">
        <div class="col-12 col-md-8 col-lg-6 col-xl-5">
            <div class="card border border-dark shadow-2-strong rounded-3">
                <div class="card-header bg-dark text-white">
                    <h1 class="fw-normal h3 text-center">LDAP Login</h1>
                </div>
                <div class="card-body p-5 text-center">
                    <div class="form-floating">
                        <?= Select::tag()
                            ->optionsData(array_map(fn(ConnectionDetails $c): string => $c->title, ConnectionDetails::getAll()))
                            ->addClass('form-control')
                            ->value($connectionId)
                            ->id('selectConfiguredConn')
                        ?>
                        <label for="floatingConnection">Connection</label>
                    </div>

                    <?= Form::tag()
                        ->post($urlGenerator->generate('login', ['c' => $connectionId]))
                        ->csrf($csrf)
                        ->id('loginForm')
                        ->open() ?>

                    <hr/>

                    <?= Field::text($formModel, 'dsn')
                        ->hint('Example: ldaps://localhost:636')
                        ->addInputAttributes(['disabled' => $formModel->isAttributeFixed('dsn')])
                    ?>

                    <?= Field::text($formModel, 'baseDn')
                        ->hint('Example: dc=example,dc=org')
                        ->addInputAttributes(['disabled' => $formModel->isAttributeFixed('baseDn')]) ?>

                    <?= Field::text($formModel, 'adminDn')
                        ->hint('Example: cn=admin,dc=example,dc=org')
                        ->addInputAttributes(['disabled' => $formModel->isAttributeFixed('adminDn')]) ?>

                    <?= Field::password($formModel, 'adminPassword')
                        ->addInputAttributes(['disabled' => $formModel->isAttributeFixed('adminPassword')]) ?>

                    <?= Field::submitButton()
                        ->name('login-button')
                        ->content('Login')
                    ?>

                    <?= Form::tag()->close() ?>

                </div>
            </div>
        </div>
    </div>
</div>
<script>
    deferJq(function () {
        $('#selectConfiguredConn').change(function () {
            url = "<?= $urlGenerator->generate('login', ['c' => 'connectionId']); ?>"
            window.location = url.replace('connectionId', $(this).val());
        });
    });

    function deferJq(method) {
        if (window.jQuery) {
            method();
        } else {
            setTimeout(function () {
                deferJq(method)
            }, 50);
        }
    }
</script>
