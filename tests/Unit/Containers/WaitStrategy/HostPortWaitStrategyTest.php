<?php

namespace Tests\Unit\Containers\WaitStrategy;

use Testcontainers\Containers\GenericContainer\GenericContainer;
use Testcontainers\Containers\WaitStrategy\ContainerStoppedException;
use Testcontainers\Containers\WaitStrategy\HostPortWaitStrategy;
use Testcontainers\Containers\WaitStrategy\WaitingTimeoutException;

class HostPortWaitStrategyTest extends WaitStrategyTestCase
{
    public function resolveWaitStrategy()
    {
        return new HostPortWaitStrategy();
    }

    public function testWaitUntilReady()
    {
        $container = new GenericContainer('nginx:alpine');
        $container->withExposedPort(80);
        $instance = $container->start();

        $strategy = new HostPortWaitStrategy();
        $strategy->waitUntilReady($instance);

        $this->assertTrue(true);
    }

    public function testWaitUntilReadyThrowsWaitingTimeoutException()
    {
        $this->expectException(WaitingTimeoutException::class);

        $container = new GenericContainer('alpine:latest');
        $container->withCommands(['sh', '-c', 'sleep 10']);
        $instance = $container->start();

        // Use withPorts to check a port that nothing is listening on
        $strategy = new HostPortWaitStrategy();
        $strategy->withPorts([59999]);
        $strategy->withTimeoutSeconds(1);
        $strategy->waitUntilReady($instance);
    }

    public function testWaitUntilReadyThrowsContainerStoppedException()
    {
        $this->expectException(ContainerStoppedException::class);

        $container = new GenericContainer('alpine:latest');
        $container->withCommands(['sh', '-c', 'sleep 1; exit 0']);
        $instance = $container->start();

        // Use withPorts to check a port that nothing is listening on
        $strategy = new HostPortWaitStrategy();
        $strategy->withPorts([59999]);
        $strategy->waitUntilReady($instance);
    }
}
