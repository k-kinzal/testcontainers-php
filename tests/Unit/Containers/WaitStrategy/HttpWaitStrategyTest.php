<?php

namespace Tests\Unit\Containers\WaitStrategy;

use Testcontainers\Containers\GenericContainer\GenericContainer;
use Testcontainers\Containers\WaitStrategy\ContainerStoppedException;
use Testcontainers\Containers\WaitStrategy\HttpWaitStrategy;
use Testcontainers\Containers\WaitStrategy\WaitingTimeoutException;

class HttpWaitStrategyTest extends WaitStrategyTestCase
{
    public function resolveWaitStrategy()
    {
        return new HttpWaitStrategy();
    }

    public function testWaitUntilReady()
    {
        $container = new GenericContainer('nginx:alpine');
        $container->withExposedPort(80);
        $instance = $container->start();

        $strategy = new HttpWaitStrategy();
        $strategy->waitUntilReady($instance);

        $this->assertTrue(true);
    }

    public function testWaitUntilReadyThrowsWaitingTimeoutException()
    {
        $this->expectException(WaitingTimeoutException::class);

        $container = new GenericContainer('alpine:latest');
        $container->withCommand('sh -c "sleep 10"');
        $container->withExposedPort(80);
        $instance = $container->start();

        $strategy = new HttpWaitStrategy();
        $strategy->withTimeout(1);
        $strategy->waitUntilReady($instance);
    }

    public function testWaitUntilReadyThrowsContainerStoppedException()
    {
        $this->expectException(ContainerStoppedException::class);

        $container = new GenericContainer('alpine:latest');
        $container->withCommand('sh -c "sleep 1; exit 0"');
        $container->withExposedPort(80);
        $instance = $container->start();

        $strategy = new HttpWaitStrategy();
        $strategy->waitUntilReady($instance);
    }
}
