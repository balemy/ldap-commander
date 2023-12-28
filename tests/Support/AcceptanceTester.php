<?php

declare(strict_types=1);

namespace Balemy\LdapCommander\Tests\Support;

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

        $I->selectOption('#loginform-sessionid', 'ldap://localhost:1389');
        $I->fillField('#loginform-password', 'secret');
        $I->click('Login', '#loginForm');

        // saving snapshot
        $this->saveSessionSnapshot('login');
    }
}
