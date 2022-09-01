<?php

declare(strict_types=1);

namespace App\Tests\Acceptance;

use App\Tests\Support\AcceptanceTester;

final class EntityManageCest
{
    public function testCreateRequireLogin(AcceptanceTester $I): void
    {
        $I->wantTo('check login is required.');
        $I->amOnPage('/edit?dn=dc%3Dexample%2Cdc%3Dorg&new=1');

        $I->expectTo('LDAP Login.');
        $I->see('LDAP Login');
    }

    public function testCreateAndDeleteOU(AcceptanceTester $I): void
    {
        $I->loggedIn();

        $I->amOnPage('/edit?dn=dc%3Dexample%2Cdc%3Dorg&new=1');
        $I->selectOptionForSelect2('#entityform-objectclass', 'organizationalunit');
        $I->fillField('EntityForm[ou][0]', 'New Department');
        $I->click('Submit', '#entityForm');

        $I->amOnPage('/browse');
        $I->see('ou=New Department,dc=example,dc=org');
        $I->click('ou=New Department,dc=example,dc=org');
        $I->see('Edit Entity');
        $I->click('Delete Entity');
        $I->acceptPopup();

        $I->amOnPage('/browse');
        $I->dontSee('ou=New Department,dc=example,dc=org');
    }


}
