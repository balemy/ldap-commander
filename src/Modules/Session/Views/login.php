<?php

declare(strict_types=1);

use Yiisoft\FormModel\Field;
use Yiisoft\Html\Tag\Form;

/**
 * @var \Yiisoft\View\WebView $this
 * @var \Yiisoft\Router\UrlGeneratorInterface $urlGenerator
 * @var string $csrf
 * @var \Balemy\LdapCommander\Modules\Session\LoginForm $loginForm
 * @var array $sessionList
 * @var \Yiisoft\Aliases\Aliases $aliases
 * @var \Balemy\LdapCommander\ApplicationParameters $applicationParameters
 */
?>

<div class="container py-5 h-100">
    <div class="row d-flex justify-content-center align-items-center h-100">
        <div class="card border border-dark shadow-2-strong rounded-3  col-md-8">
            <div class="card-header bg-dark text-white">
                <h1 class="fw-normal h3 text-center">LDAP Login</h1>
            </div>
            <div class="card-body p-5" style="">
                <?php if (!empty($applicationParameters->loginMessage)): ?>
                    <div class="alert alert-primary" role="alert">
                        <?= $applicationParameters->loginMessage ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($sessionList)): ?>
                    <?= Form::tag()->post($urlGenerator->generate('login', []))->csrf($csrf)->id('loginForm')->open() ?>
                    <div class="form-floating">
                        <?= Field::select($loginForm, 'sessionId')->optionsData($sessionList)->autofocus(); ?>
                    </div>
                    <div class="row">
                        <div class="col-md">
                            <?= Field::text($loginForm, 'username')->autofocus(); ?>
                        </div>
                        <div class="col-md">
                            <?= Field::password($loginForm, 'password') ?>
                        </div>
                    </div>

                    <?= Field::submitButton()->name('login-button')->content('Login') ?>
                    <?= Form::tag()->close() ?>
                <?php else: ?>
                    <div class="alert alert-danger" role="alert">
                        <p><strong>Configuration File missing!</strong></p>
                        <p>Please create the file <code>/config/ldap.php</code> an example can be found
                            here: <code><?= $aliases->get('@root') ?>/ldap.php</code></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
