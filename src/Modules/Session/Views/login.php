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

<style>

    html,
    body {
        height: 100%;
    }

    body {
        display: flex;
        align-items: center;
        padding-top: 40px;
        padding-bottom: 40px;
        background-color: #f5f5f5;
    }

    .form-signin {
        width: 100%;
        max-width: 430px;
        padding: 15px;
        margin: auto;
    }

    .form-signin .checkbox {
        font-weight: 400;
    }

    .form-signin .form-floating:focus-within {
        z-index: 2;
    }

    .form-signin input[type="email"] {
        margin-bottom: -1px;
        border-bottom-right-radius: 0;
        border-bottom-left-radius: 0;
    }

    .form-signin input[type="password"] {
        margin-bottom: 10px;
        border-top-left-radius: 0;
        border-top-right-radius: 0;
    }
    body {
        text-align:center!important;
    }
</style>
<main class="form-signin">
    <?= Form::tag()->post($urlGenerator->generate('login', []))->csrf($csrf)->id('loginForm')->open() ?>
        <img class="mb-4" src="https://dummyimage.com/300" alt="" width="72">
        <h1 class="h3 mb-3 fw-normal">Please sign in</h1>

        <?php if (!empty($applicationParameters->loginMessage)): ?>
            <div class="alert alert-primary" role="alert">
                <?= $applicationParameters->loginMessage ?>
            </div>
        <?php endif; ?>

        <div class="form-floating">
            <?= Field::select($loginForm, 'sessionId')->optionsData($sessionList)->autofocus(); ?>
        </div>
        <div class="form-floating">
            <?= Field::text($loginForm, 'username')->autofocus(); ?>
        </div>
        <div class="form-floating">
            <?= Field::password($loginForm, 'password') ?>
        </div>

        <?= Field::submitButton()->name('login-button')->content('Login') ?>
        <?= Form::tag()->close() ?>
    </form>
</main>

