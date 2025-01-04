<?php

namespace Tests\Unit\Containers\WaitStrategy\PDO;

use LogicException;
use Testcontainers\Containers\GenericContainer\GenericContainerInstance;
use Testcontainers\Containers\WaitStrategy\PDO\PDOConnectWaitStrategy;
use Testcontainers\Containers\WaitStrategy\PDO\SQLiteDSN;
use Tests\Unit\Containers\WaitStrategy\WaitStrategyTestCase;

class PDOConnectWaitStrategyTest extends WaitStrategyTestCase
{
    /**
     * @inheritDoc
     */
    public function resolveWaitStrategy()
    {
        return (new PDOConnectWaitStrategy())
            ->withDsn(new SQLiteDSN());
    }

    public function testWaitUntilReady()
    {
        $instance = new GenericContainerInstance('818b7f3b1b3b', [
            'ports' => [3306 => 3306],
        ]);
        $strategy = $this->resolveWaitStrategy();
        $strategy->waitUntilReady($instance);

        $this->assertTrue(true);
    }

    public function testWaitUntilReadyNotSetDSN()
    {
        $this->expectException(LogicException::class);

        $instance = new GenericContainerInstance('818b7f3b1b3b', [
            'ports' => [3306 => 3306],
        ]);
        $strategy = new PDOConnectWaitStrategy();
        $strategy->waitUntilReady($instance);
    }

    public function testWaitUntilReadyNotSetPort()
    {
        $this->expectException(LogicException::class);

        $instance = new GenericContainerInstance('818b7f3b1b3b');
        $strategy = $this->resolveWaitStrategy();
        $strategy->waitUntilReady($instance);
    }
}
