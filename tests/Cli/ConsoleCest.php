<?php

declare(strict_types=1);

namespace Balemy\LdapCommander\Tests\Cli;

use Balemy\LdapCommander\Tests\Support\CliTester;

final class ConsoleCest
{
    public function testCommandYii(CliTester $I): void
    {
        $command = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'cmda';
        $I->runShellCommand($command);
        $I->seeInShellOutput('Yii Console');
    }

    public function testCommandHello(CliTester $I): void
    {
        $command = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'cmda';
        $I->runShellCommand($command . ' hello');
        $I->seeInShellOutput('Hello!');
    }
}
