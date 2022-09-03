<?php

declare(strict_types=1);

namespace App\Tests\Acceptance;

use App\Tests\Support\AcceptanceTester;

final class EntityBrowserCest
{
    public function testIndexPageWithoutLogin(AcceptanceTester $I): void
    {
        $I->wantTo('check login is required.');
        $I->amOnPage('/entity/browse');

        $I->expectTo('LDAP Login.');
        $I->see('LDAP Login');
    }

    public function testIndexPage(AcceptanceTester $I): void
    {
        $I->loggedIn();

        $I->wantTo('want to see overview');
        $I->amOnPage('/entity/browse');

        $I->expectTo('See base DN Children.');
        $I->see('List Children');
        $I->see('ou=users');
    }


    public function testClickWithoutChildren(AcceptanceTester $I): void
    {
        $I->loggedIn();

        $I->wantTo('want to see overview');
        $I->amOnPage('/entity/browse?dn=ou%3Dusers%2Cdc%3Dexample%2Cdc%3Dorg');
        $I->click('cn=readers,ou=users,dc=example,dc=org');

        $I->expectTo('See Edit');
        $I->see('Edit Entity');
    }

    public function testClickWithChildren(AcceptanceTester $I): void
    {
        $I->loggedIn();

        $I->wantTo('want to see overview');
        $I->amOnPage('/entity/browse');
        $I->click('ou=users,dc=example,dc=org');

        $I->expectTo('See Children');
        $I->see('List Children');
        $I->see('cn=user01');
    }
}
