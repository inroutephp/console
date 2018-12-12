<?php

declare(strict_types = 1);

namespace inroutephp\console;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Console\Output\OutputInterface;

class DebugCommandTest extends \PHPUnit\Framework\TestCase
{
    public function testDebug()
    {
        $application = new Application();
        $application->add(new DebugCommand());

        $command = $application->find('debug');
        $commandTester = new CommandTester($command);
        $commandTester->execute(
            [
                '--no-configuration' => true
            ],
            [
                'verbosity' => OutputInterface::VERBOSITY_VERBOSE
            ]
        );

        $this->assertRegExp("/Name/", $commandTester->getDisplay());
    }
}
