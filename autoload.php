<?php

declare(strict_types=1);

use App\Ldap\ConnectionDetails;
use Dotenv\Dotenv;

require_once __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();


foreach (ConnectionDetails::ENV_CONFIG_MAP as $v) {
    if (!empty($_SERVER[$v]) && is_string($_SERVER[$v])) {
        $_ENV[$v] = $_SERVER[$v];
    }
}

$_ENV['YII_ENV'] = empty($_ENV['YII_ENV']) ? null : (string)$_ENV['YII_ENV'];
$_SERVER['YII_ENV'] = $_ENV['YII_ENV'];

$_ENV['YII_DEBUG'] = filter_var(
        !empty($_ENV['YII_DEBUG']) ? $_ENV['YII_DEBUG'] : true,
        FILTER_VALIDATE_BOOLEAN,
        FILTER_NULL_ON_FAILURE
    ) ?? true;
$_SERVER['YII_DEBUG'] = $_ENV['YII_DEBUG'];
