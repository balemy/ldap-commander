<?php

declare(strict_types=1);

namespace Balemy\LdapCommander\Tests\Acceptance;

use Balemy\LdapCommander\Tests\Support\AcceptanceTester;

final class EntityManageCest
{
    public function testCreateRequireLogin(AcceptanceTester $I): void
    {
        $I->wantTo('check login is required.');
        $I->amOnPage('/entity/edit?dn=dc%3Dexample%2Cdc%3Dorg&new=1');

        $I->expectTo('LDAP Login.');
        $I->see('LDAP Login');
    }

    public function testCreateAndDeleteOU(AcceptanceTester $I): void
    {
        $I->loggedIn();

        $I->amOnPage('/entity/edit?dn=dc%3Dexample%2Cdc%3Dorg&new=1');
        $I->selectOptionForSelect2('#entityform-objectclass', 'organizationalunit');
        $I->fillField('EntityForm[ou][0]', 'New Department');
        $I->click('Submit', '#entityForm');

        $I->amOnPage('/entity/browse');
        $I->see('ou=New Department,dc=example,dc=org');
        $I->click('ou=New Department,dc=example,dc=org');
        $I->see('Edit Entity');
        $I->click('Delete Entity');
        $I->acceptPopup();

        $I->amOnPage('/entity/browse');
        $I->dontSee('ou=New Department,dc=example,dc=org');
    }

    public function testDuplicate(AcceptanceTester $I): void
    {
        $I->loggedIn();
        $I->amOnPage('/entity/edit?dn=cn%3DVPN+Users%2Cou%3DGroups%2Cdc%3Dexample%2Cdc%3Dorg');
        $I->see('Edit Entity');
        $I->click('Duplicate Entity');
        $I->see('Add Entity');
        $I->seeInFormFields('#entityForm', [
            'EntityForm[cn][0]' => 'VPN Users',
            'EntityForm[uniquemember][0]' => 'cn=Colman Gasikowski,ou=Janitorial,dc=example,dc=org',
            'EntityForm[uniquemember][1]' => 'cn=Ineke Silburt,ou=Product Development,dc=example,dc=org',
            'EntityForm[uniquemember][2]' => 'cn=Kristopher Rosenthal,ou=Administrative,dc=example,dc=org',
        ]);

        $I->fillField('EntityForm[cn][0]', 'Secure Users');
        $I->click('Submit');

        $I->amOnPage('/entity/edit?dn=cn%3DSecure+Users%2Cou%3DGroups%2Cdc%3Dexample%2Cdc%3Dorg');
        $I->click('Delete Entity');
        $I->acceptPopup();


    }
}
