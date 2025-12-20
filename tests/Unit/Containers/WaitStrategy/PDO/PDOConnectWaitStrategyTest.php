<?php

namespace Tests\Unit\Containers\WaitStrategy\PDO;

use Testcontainers\Containers\GenericContainer\GenericContainer;
use Testcontainers\Containers\WaitStrategy\ContainerStoppedException;
use Testcontainers\Containers\WaitStrategy\PDO\MySQLDSN;
use Testcontainers\Containers\WaitStrategy\PDO\PDOConnectWaitStrategy;
use Testcontainers\Containers\WaitStrategy\PDO\SQLiteDSN;
use Testcontainers\Containers\WaitStrategy\WaitingTimeoutException;
use Tests\Unit\Containers\WaitStrategy\WaitStrategyTestCase;

class PDOConnectWaitStrategyTest extends WaitStrategyTestCase
{
    public function resolveWaitStrategy()
    {
        return (new PDOConnectWaitStrategy())->withDsn(new SQLiteDSN());
    }

    public function testWaitUntilReady()
    {
        $container = new GenericContainer('alpine:latest');
        $container->withCommands(['sh', '-c', 'sleep 10']);
        $instance = $container->start();

        $strategy = (new PDOConnectWaitStrategy())->withDsn(new SQLiteDSN());
        $strategy->waitUntilReady($instance);

        $this->assertTrue(true);
    }

    public function testWaitUntilReadyThrowsWaitingTimeoutException()
    {
        $this->expectException(WaitingTimeoutException::class);

        $container = new GenericContainer('alpine:latest');
        $container->withCommands(['sh', '-c', 'sleep 10']);
        $container->withExposedPort(3306);
        $instance = $container->start();

        $strategy = (new PDOConnectWaitStrategy())->withDsn(new MySQLDSN('test'));
        $strategy->withTimeoutSeconds(1);
        $strategy->waitUntilReady($instance);
    }

    public function testWaitUntilReadyThrowsContainerStoppedException()
    {
        $this->expectException(ContainerStoppedException::class);

        $container = new GenericContainer('alpine:latest');
        $container->withCommands(['sh', '-c', 'sleep 1; exit 0']);
        $container->withExposedPort(3306);
        $instance = $container->start();

        $strategy = (new PDOConnectWaitStrategy())->withDsn(new MySQLDSN('test'));
        $strategy->waitUntilReady($instance);
    }
}
