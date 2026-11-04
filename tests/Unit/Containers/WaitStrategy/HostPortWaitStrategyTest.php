<?php

namespace Tests\Unit\Containers\WaitStrategy;

use Testcontainers\Containers\GenericContainer\GenericContainer;
use Testcontainers\Containers\WaitStrategy\ContainerStoppedException;
use Testcontainers\Containers\WaitStrategy\HostPortWaitStrategy;
use Testcontainers\Containers\WaitStrategy\WaitingTimeoutException;

final class HostPortWaitStrategyTest extends WaitStrategyTestCase
{
    /**
     * @return void
     */
    public function testWaitUntilReady()
    {
        $container = new GenericContainer('nginx:alpine');
        $container->withStopTimeout(0);
        $container->withExposedPort(80);
        $container->withAutoRemoveOnExit(true);
        $instance = $container->start();

        $strategy = new HostPortWaitStrategy();
        $strategy->waitUntilReady($instance);

        self::assertTrue(true);
    }

    /**
     * @return void
     */
    public function testWaitUntilReadyThrowsWaitingTimeoutException()
    {
        $this->expectException(WaitingTimeoutException::class);

        $container = new GenericContainer('alpine:latest');
        $container->withStopTimeout(0);
        $container->withCommands(['sh', '-c', 'sleep 10']);
        $container->withAutoRemoveOnExit(true);
        $instance = $container->start();

        // Use withPorts to check a port that nothing is listening on
        $strategy = new HostPortWaitStrategy();
        $strategy->withPorts([59999]);
        $strategy->withTimeoutSeconds(1);
        $strategy->waitUntilReady($instance);
    }

    /**
     * @return void
     */
    public function testWaitUntilReadyThrowsContainerStoppedException()
    {
        $this->expectException(ContainerStoppedException::class);

        $container = new GenericContainer('alpine:latest');
        $container->withStopTimeout(0);
        $container->withCommands(['sh', '-c', 'sleep 1; exit 0']);
        $container->withAutoRemoveOnExit(true);
        $instance = $container->start();

        // Use withPorts to check a port that nothing is listening on
        $strategy = new HostPortWaitStrategy();
        $strategy->withPorts([59999]);
        $strategy->waitUntilReady($instance);
    }
}
