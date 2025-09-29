<?php

declare(strict_types=1);

namespace Balemy\LdapCommander\Tests\Acceptance;

use Balemy\LdapCommander\Tests\Support\AcceptanceTester;

final class LoginCest
{
    public function testLoginSuccess(AcceptanceTester $I): void
    {
        $I->amGoingTo('go to the log in page.');
        $I->amOnPage('/login');


        $I->selectOption('#loginform-sessionid', 'ldap://localhost:1389');
        $I->fillField('#loginform-password', 'secret');
        $I->click('Login', '#loginForm');

        $I->expectTo('see logged index page.');
        $I->dontSee('Please sign in');

        $I->expectTo('See Homepage Browse');
        $I->see('Overview of all users');
    }

    public function testLoginFail(AcceptanceTester $I): void
    {
        $I->amGoingTo('go to the log in page.');
        $I->amOnPage('/login');

        $I->selectOption('#loginform-sessionid', 'ldap://localhost:1389');
        $I->fillField('#loginform-password', 'wrong-password');
        $I->click('Login', '#loginForm');

        $I->expectTo('see validations errors.');
        $I->see('Login failed!');
    }
}
