<?php

declare(strict_types=1);

use Yiisoft\Form\Field;
use Yiisoft\Html\Tag\Form;

/**
 * @var \Yiisoft\View\WebView $this
 * @var \Yiisoft\Router\UrlGeneratorInterface $urlGenerator
 * @var string $csrf
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

                    <?= Form::tag()
                        ->post($urlGenerator->generate('login'))
                        ->csrf($csrf)
                        ->id('loginForm')
                        ->open() ?>

                    <?= Field::text($formModel, 'dsn')
                        ->hint('Example: ldaps://localhost:636')
                        ->addInputAttributes(['disabled' => $formModel->isAttributeFixed('dsn')])
                        ->autofocus() ?>

                    <?= Field::text($formModel, 'baseDn')
                        ->hint('Example: dc=example,dc=org')
                        ->addInputAttributes(['disabled' => $formModel->isAttributeFixed('baseDn')]) ?>

                    <?= Field::text($formModel, 'adminDn')
                        ->hint('Example: cn=admin,dc=example,dc=org')
                        ->addInputAttributes(['disabled' => $formModel->isAttributeFixed('adminDn')]) ?>

                    <?= Field::password($formModel, 'adminPassword')
                        ->addInputAttributes(['disabled' => $formModel->isAttributeFixed('adminPasswordadm,')]) ?>

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
