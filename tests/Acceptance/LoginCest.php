<?php

declare(strict_types=1);

namespace App\Tests\Acceptance;

use App\Tests\Support\AcceptanceTester;

final class LoginCest
{
    public function testIndexPage(AcceptanceTester $I): void
    {
        $I->wantTo('login page works.');
        $I->amOnPage('/login');
        $I->expectTo('LDAP Login.');
        $I->see('LDAP Login');
    }

    public function testLoginEmptyDataTest(AcceptanceTester $I): void
    {
        $I->amGoingTo('go to the log in page.');
        $I->amOnPage('/login');

        $I->fillField('#login-adminpassword', '');

        $I->click('Login', '#loginForm');

        $I->expectTo('see validations errors.');
        $I->see('Value cannot be blank');

        $I->seeElement('button', ['name' => 'login-button']);
    }

    public function testLoginSubmitFormWrongDataPassword(AcceptanceTester $I): void
    {
        $I->amGoingTo('go to the log in page.');
        $I->amOnPage('/login');

        $I->fillField('#login-dsn', 'ldap://127.0.0.1:1389');
        $I->fillField('#login-basedn', 'dc=example,dc=org');
        $I->fillField('#login-admindn', 'cn=admin,dc=example,dc=org');
        $I->fillField('#login-adminpassword', 'wrong');

        $I->click('Login', '#loginForm');

        $I->expectTo('see validations errors.');
        $I->see('Unable to bind to server: Invalid credentials');

        $I->seeElement('button', ['name' => 'login-button']);
    }

    public function testLoginUsernameSubmitFormSuccessData(AcceptanceTester $I): void
    {
        $I->amGoingTo('go to the log in page.');
        $I->amOnPage('/login');

        $I->fillField('#login-dsn', 'ldap://127.0.0.1:1389');
        $I->fillField('#login-basedn', 'dc=example,dc=org');
        $I->fillField('#login-admindn', 'cn=admin,dc=example,dc=org');
        $I->fillField('#login-adminpassword', 'secret');

        $I->click('Login', '#loginForm');

        $I->expectTo('see logged index page.');
        $I->dontSee('LDAP Login');

        $I->expectTo('See Homepage Browse');
        $I->see('List Children');
    }

}
