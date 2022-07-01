<?php

declare(strict_types=1);

namespace Sifo\Test\Test;

use Psr\Container\ContainerInterface;
use PHPUnit\Framework\TestCase;
use Sifo\Test\Common\TestConsoleScriptRunner;

/**
 * @runTestsInSeparateProcesses
 */
class CLBootstrapTest extends TestCase
{
    public function testRunFakeCommand(): void
    {
        $testRunner = new TestConsoleScriptRunner(
            'sifo.local',
            '\Sifo\Example\Console\TestConsoleCommand',
            ROOT_PATH . '/example/Console/TestConsoleCommandController.php',
            [
                ROOT_PATH . '/example/Console/TestConsoleCommandController.php',
                'sifo.local',
            ]
        );

        $output = $testRunner->run($this->createMock(ContainerInterface::class));

        self::assertStringContainsString('Hello World!', $output);
    }
}
