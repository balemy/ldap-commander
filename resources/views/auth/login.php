<?php

declare(strict_types=1);

use Yiisoft\Form\FormModelInterface;
use Yiisoft\Form\Widget\Field;
use Yiisoft\Form\Widget\Form;

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
                    <?= Form::widget()
                        ->action($urlGenerator->generate('login'))
                        ->csrf($csrf)
                        ->id('loginForm')
                        ->begin() ?>
                    <?= Field::widget()->text($formModel, 'dsn')->hint('Example: ldaps://localhost:636')->attributes(['disabled' => $formModel->isAttributeFixed('dsn')]) ?>
                    <?= Field::widget()->text($formModel, 'baseDn')->hint('Example: dc=example,dc=org')->attributes(['disabled' => $formModel->isAttributeFixed('baseDn')]) ?>
                    <?= Field::widget()->autofocus()->text($formModel, 'adminDn')->hint('Example: cn=admin,dc=example,dc=org')->attributes(['disabled' => $formModel->isAttributeFixed('adminDn')]) ?>
                    <?= Field::widget()->password($formModel, 'adminPassword')->attributes(['disabled' => $formModel->isAttributeFixed('adminPasswordadm,')]) ?>

                    <?= Field::widget()
                        ->id('login-button')
                        ->name('login-button')
                        ->submitButton()
                        ->value('Login')
                    ?>

                    <?= Form::end() ?>
                </div>
            </div>
        </div>
    </div>
</div>
