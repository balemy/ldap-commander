<?php

declare(strict_types=1);

namespace App\Tests\Acceptance;

use App\Tests\Support\AcceptanceTester;

final class LoginCest
{
    public function testLoginSuccess(AcceptanceTester $I): void
    {
        $I->amGoingTo('go to the log in page.');
        $I->amOnPage('/login');

        $I->selectOption('#selectConfiguredConn', 'Custom');
        $I->amOnPage('/login?c=99');
        $I->fillField('#login-dsn', 'ldap://127.0.0.1:1389');
        $I->fillField('#login-basedn', 'dc=example,dc=org');
        $I->fillField('#login-admindn', 'cn=admin,dc=example,dc=org');
        $I->fillField('#login-adminpassword', 'secret');
        $I->click('Login', '#loginForm');

        $I->expectTo('see logged index page.');
        $I->dontSee('LDAP Login');

        $I->expectTo('See Homepage Browse');
        $I->see('List Children');
        $I->see('ou=users,dc=example,dc=org');
    }

    public function testLoginFail(AcceptanceTester $I): void
    {
        $I->amGoingTo('go to the log in page.');
        $I->amOnPage('/login');

        $I->selectOption('#selectConfiguredConn', 'Custom');
        $I->amOnPage('/login?c=99');
        $I->fillField('#login-dsn', 'ldap://127.0.0.1:1389');
        $I->fillField('#login-basedn', 'dc=example,dc=org');
        $I->fillField('#login-admindn', 'cn=admin,dc=example,dc=org');
        $I->fillField('#login-adminpassword', 'wrong');

        $I->click('Login', '#loginForm');

        $I->expectTo('see validations errors.');
        $I->see('Unable to bind to server: Invalid credentials');
    }
}
