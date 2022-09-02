<?php

declare(strict_types=1);

namespace App\Tests\Acceptance;

use App\Tests\Support\AcceptanceTester;

final class SchemaBrowserCest
{
    public function testIndex(AcceptanceTester $I): void
    {
        $I->loggedIn();
        $I->amOnPage('/schema');
        $I->see('top');
        $I->click('top');
        $I->see('Object Class: top');
    }

    public function testObjectClassView(AcceptanceTester $I): void
    {
        $I->loggedIn();
        $I->amOnPage('/schema/object-class?oid=2.5.6.6');
        $I->see('Object Class: person');
        $I->see('userPassword');
        $I->see('cn, commonName');
        $I->click('userPassword');
        $I->see('Attribute: userPassword');
    }

    public function testAttributeView(AcceptanceTester $I): void
    {
        $I->loggedIn();
        $I->amOnPage('/schema/attribute?oid=2.5.4.13');
        $I->see('Attribute: description');
        $I->see('caseIgnoreSubstringsMatch');
        $I->see('country');
    }
}
