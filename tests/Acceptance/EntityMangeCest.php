<?php

declare(strict_types=1);

namespace App\Tests\Acceptance;

use App\Tests\Support\AcceptanceTester;

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
        $I->amOnPage('/entity/edit?dn=cn%3Duser01%2Cou%3Dusers%2Cdc%3Dexample%2Cdc%3Dorg');
        $I->see('Edit Entity');
        $I->click('Duplicate Entity');
        $I->see('Add Entity');
        $I->seeInFormFields('#entityForm', [
            'EntityForm[cn][0]' => 'User1',
            'EntityForm[cn][1]' => 'user01',
            'EntityForm[uid][0]' => 'user01',
            'EntityForm[uidnumber][0]' => '1000',
            'EntityForm[gidnumber][0]' => '1000',
            'EntityForm[homedirectory][0]' => '/home/user01',
        ]);

        $I->fillField('EntityForm[cn][0]', 'User9');
        $I->fillField('EntityForm[cn][1]', 'user09');
        $I->fillField('EntityForm[uid][0]', 'user09');
        $I->fillField('EntityForm[uidnumber][0]', '9000');
        $I->fillField('EntityForm[gidnumber][0]', '9000');
        $I->fillField('EntityForm[homedirectory][0]', '/home/user09');

        $I->click('Submit');

        $I->amOnPage('/entity/edit?dn=cn%3DUser9%2Cou%3Dusers%2Cdc%3Dexample%2Cdc%3Dorg');
        $I->click('Delete Entity');
        $I->acceptPopup();


    }
}
