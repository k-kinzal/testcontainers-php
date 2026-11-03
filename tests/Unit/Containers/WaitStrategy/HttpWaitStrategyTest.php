<?php

namespace Tests\Unit\Containers\WaitStrategy;

use Testcontainers\Containers\GenericContainer\GenericContainer;
use Testcontainers\Containers\WaitStrategy\ContainerStoppedException;
use Testcontainers\Containers\WaitStrategy\HttpWaitStrategy;
use Testcontainers\Containers\WaitStrategy\WaitingTimeoutException;
use UnexpectedValueException;

final class HttpWaitStrategyTest extends WaitStrategyTestCase
{
    /**
     * @return void
     */
    public function testWaitUntilReady()
    {
        $container = new GenericContainer('nginx:alpine');
        $container->withExposedPort(80);
        $instance = $container->start();

        $strategy = new HttpWaitStrategy();
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
        $container->withCommands(['sh', '-c', 'sleep 10']);
        $container->withExposedPort(80);
        $instance = $container->start();

        $strategy = new HttpWaitStrategy();
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
        $container->withCommands(['sh', '-c', 'sleep 1; exit 0']);
        $container->withExposedPort(80);
        $instance = $container->start();

        $strategy = new HttpWaitStrategy();
        $strategy->waitUntilReady($instance);
    }

    /**
     * @return void
     */
    public function testWithTimeoutSecondsRejectsNegativeTimeout()
    {
        $this->expectException(UnexpectedValueException::class);

        (new HttpWaitStrategy())->withTimeoutSeconds(-1);
    }

    /**
     * @return void
     */
    public function testWithRetryIntervalRejectsStringInterval()
    {
        $this->expectException(UnexpectedValueException::class);

        (new HttpWaitStrategy())->withRetryInterval('1');
    }

    /**
     * @return void
     */
    public function testWithExpectedResponseCodeRejectsOutOfRangeCode()
    {
        $this->expectException(UnexpectedValueException::class);

        (new HttpWaitStrategy())->withExpectedResponseCode(99);
    }
}
