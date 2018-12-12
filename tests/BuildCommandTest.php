<?php

declare(strict_types = 1);

namespace inroutephp\console;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Console\Output\OutputInterface;

class BuildCommandTest extends \PHPUnit\Framework\TestCase
{
    public function testBuild()
    {
        $application = new Application();
        $application->add(new BuildCommand());

        $target = tempnam(sys_get_temp_dir(), 'inroute');

        $command = $application->find('build');
        $commandTester = new CommandTester($command);
        $commandTester->execute(
            [
                '--target-filename' => $target,
                '--no-configuration' => true,
            ],
            [
                'verbosity' => OutputInterface::VERBOSITY_VERBOSE
            ]
        );

        $this->assertRegExp('/HttpRouter/', file_get_contents($target));

        $quotedTarget = preg_quote($target, '/');

        $this->assertRegExp("/$quotedTarget/", $commandTester->getDisplay());

        unlink($target);
    }
}
