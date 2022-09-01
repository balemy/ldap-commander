<?php

declare(strict_types=1);

namespace App\Tests\Support;

/**
 * Inherited Methods
 *
 * @method void wantToTest($text)
 * @method void wantTo($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method void pause($vars = [])
 *
 * @SuppressWarnings(PHPMD)
 */
class AcceptanceTester extends \Codeception\Actor
{
    use _generated\AcceptanceTesterActions;

    /**
     * Define custom actions here
     */

    public function loggedIn()
    {
        // if snapshot exists - skipping login
        if ($this->loadSessionSnapshot('login')) {
            return;
        }

        $I = $this;
        $I->amOnPage('/login');
        try {
            $I->fillField('#login-dsn', 'ldap://127.0.0.1:1389');
            $I->fillField('#login-basedn', 'dc=example,dc=org');
            $I->fillField('#login-admindn', 'cn=admin,dc=example,dc=org');
        } catch (\Exception $ex) {

        }
        $I->fillField('#login-adminpassword', 'secret');
        $I->click('Login');

        // saving snapshot
        $this->saveSessionSnapshot('login');
    }
}
