<?php

namespace Tests\Unit\Containers\WaitStrategy\PDO;

use stdClass;
use Testcontainers\Containers\GenericContainer\GenericContainer;
use Testcontainers\Containers\WaitStrategy\ContainerStoppedException;
use Testcontainers\Containers\WaitStrategy\PDO\MySQLDSN;
use Testcontainers\Containers\WaitStrategy\PDO\PDOConnectWaitStrategy;
use Testcontainers\Containers\WaitStrategy\PDO\SQLiteDSN;
use Testcontainers\Containers\WaitStrategy\WaitingTimeoutException;
use Tests\Unit\Containers\WaitStrategy\WaitStrategyTestCase;
use UnexpectedValueException;

final class PDOConnectWaitStrategyTest extends WaitStrategyTestCase
{
    /**
     * @return void
     */
    public function testWaitUntilReady()
    {
        $container = new GenericContainer('alpine:latest');
        $container->withStopTimeout(0);
        $container->withCommands(['sh', '-c', 'sleep 10']);
        $instance = $container->start();

        $strategy = (new PDOConnectWaitStrategy())->withDsn(new SQLiteDSN());
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
        $container->withExposedPort(3306);
        $instance = $container->start();

        $strategy = (new PDOConnectWaitStrategy())->withDsn((new MySQLDSN())->withDbname('test'));
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
        $container->withExposedPort(3306);
        $instance = $container->start();

        $strategy = (new PDOConnectWaitStrategy())->withDsn((new MySQLDSN())->withDbname('test'));
        $strategy->waitUntilReady($instance);
    }

    /**
     * @return void
     */
    public function testWithDsnRejectsUnrelatedObject()
    {
        $this->expectException(UnexpectedValueException::class);

        (new PDOConnectWaitStrategy())->withDsn(new stdClass());
    }
}
