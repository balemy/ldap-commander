<?php

declare(strict_types=1);

namespace Balemy\LdapCommander\Tests\Unit;


use Balemy\LdapCommander\LDAP\Schema\Schema;
use Balemy\LdapCommander\Tests\Support\UnitTester;
use function dirname;

final class LdapObjectClassTest extends \Codeception\Test\Unit
{
    protected UnitTester $tester;

    protected function _before()
    {
    }

    // tests
    public function testMe()
    {

        $this->assertFalse(false);
    }

}
