<?php

declare(strict_types=1);

namespace Balemy\LdapCommander\Tests\Acceptance;

use Balemy\LdapCommander\Tests\Support\AcceptanceTester;

final class EntityBrowserCest
{
    public function testIndexPageWithoutLogin(AcceptanceTester $I): void
    {
        $I->wantTo('check login is required.');
        $I->amOnPage('/entity/browse');

        $I->expectTo('Please sign in.');
        $I->see('Please sign in');
    }

    public function testIndexPage(AcceptanceTester $I): void
    {
        $I->loggedIn();

        $I->wantTo('want to see overview');
        $I->amOnPage('/entity/browse');

        $I->expectTo('See base DN Children.');
        $I->see('List Children');
        $I->see('ou=Accounting');
    }


    public function testClickWithoutChildren(AcceptanceTester $I): void
    {
        $I->loggedIn();

        $I->wantTo('want to see overview');
        $I->amOnPage('/entity/browse?dn=ou%3DProduct+Development%2Cdc%3Dexample%2Cdc%3Dorg');
        $I->click('cn=Felipe Sarioglu,ou=Product Development,dc=example,dc=org');

        $I->expectTo('See Edit');
        $I->see('Edit Entity');
    }

    public function testClickWithChildren(AcceptanceTester $I): void
    {
        $I->loggedIn();

        $I->wantTo('want to see overview');
        $I->amOnPage('/entity/browse');
        $I->click('ou=Groups,dc=example,dc=org');

        $I->expectTo('See Children');
        $I->see('List Children');
        $I->see('cn=VPN Users');
    }
}
