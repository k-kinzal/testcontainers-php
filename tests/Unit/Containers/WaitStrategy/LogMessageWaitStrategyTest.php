<?php

namespace Tests\Unit\Containers\WaitStrategy;

use Testcontainers\Containers\GenericContainer\GenericContainer;
use Testcontainers\Containers\WaitStrategy\ContainerStoppedException;
use Testcontainers\Containers\WaitStrategy\LogMessageFailedException;
use Testcontainers\Containers\WaitStrategy\LogMessageWaitStrategy;
use Testcontainers\Containers\WaitStrategy\WaitingTimeoutException;

/**
 * @internal
 *
 * @coversNothing
 */
class LogMessageWaitStrategyTest extends WaitStrategyTestCase
{
    public function resolveWaitStrategy()
    {
        return new LogMessageWaitStrategy();
    }

    public function testWaitUntilReady()
    {
        $container = new GenericContainer('alpine:latest');
        $container->withCommands(['sh', '-c', 'while true; do echo "Ready"; sleep 1; done']);
        $instance = $container->start();

        $strategy = new LogMessageWaitStrategy();
        $strategy->withPattern('Ready');
        $strategy->waitUntilReady($instance);

        $this->assertTrue(true);
    }

    public function testWaitUntilReadyThrowsWaitingTimeoutException()
    {
        $this->expectException(WaitingTimeoutException::class);

        $container = new GenericContainer('alpine:latest');
        $container->withCommands(['sh', '-c', 'while true; do echo "Not Ready"; sleep 1; done']);
        $instance = $container->start();

        $strategy = new LogMessageWaitStrategy();
        $strategy->withPattern('^Ready$');
        $strategy->withTimeoutSeconds(1);
        $strategy->waitUntilReady($instance);
    }

    public function testWaitUntilReadyThrowsContainerStoppedException()
    {
        $this->expectException(ContainerStoppedException::class);
        $this->expectExceptionMessage('Container stopped while waiting for log message');

        $container = new GenericContainer('alpine:latest');
        $container->withCommands(['sh', '-c', 'echo "Wrong message"; exit 0']);
        $instance = $container->start();

        $strategy = new LogMessageWaitStrategy();
        $strategy->withPattern('Right message');

        $strategy->waitUntilReady($instance);
    }

    public function testWaitUntilReadyThrowsLogMessageFailedException()
    {
        $this->expectException(LogMessageFailedException::class);
        $this->expectExceptionMessage('Container log matched failure pattern');

        $container = new GenericContainer('alpine:latest');
        $container->withCommands(['sh', '-c', 'echo "[ERROR] something failed"; sleep 10']);
        $instance = $container->start();

        $strategy = new LogMessageWaitStrategy();
        $strategy->withPattern('Ready');
        $strategy->withFailurePattern('\\[ERROR\\]');
        $strategy->withTimeoutSeconds(5);
        $strategy->waitUntilReady($instance);
    }
}
