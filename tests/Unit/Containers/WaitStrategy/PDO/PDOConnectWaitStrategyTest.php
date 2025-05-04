<?php

namespace Tests\Unit\Containers\WaitStrategy\PDO;

use Testcontainers\Containers\GenericContainer\GenericContainerInstance;
use Testcontainers\Containers\WaitStrategy\PDO\PDOConnectWaitStrategy;
use Testcontainers\Containers\WaitStrategy\PDO\SQLiteDSN;
use Testcontainers\Docker\Types\ContainerId;
use Tests\Unit\Containers\WaitStrategy\WaitStrategyTestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class PDOConnectWaitStrategyTest extends WaitStrategyTestCase
{
    public function resolveWaitStrategy()
    {
        return (new PDOConnectWaitStrategy())
            ->withDsn(new SQLiteDSN())
        ;
    }

    public function testWaitUntilReady()
    {
        $instance = new GenericContainerInstance([
            'containerId' => new ContainerId('8188d93d8a27'),
            'ports' => [3306 => 3306],
        ]);
        $strategy = $this->resolveWaitStrategy();
        $strategy->waitUntilReady($instance);

        $this->assertTrue(true);
    }

    public function testWaitUntilReadyNotSetDSN()
    {
        $this->expectException(\LogicException::class);

        $instance = new GenericContainerInstance([
            'containerId' => new ContainerId('8188d93d8a27'),
            'ports' => [3306 => 3306],
        ]);
        $strategy = new PDOConnectWaitStrategy();
        $strategy->waitUntilReady($instance);
    }

    public function testWaitUntilReadyNotSetPort()
    {
        $this->expectException(\LogicException::class);

        $instance = new GenericContainerInstance([
            'containerId' => new ContainerId('8188d93d8a27'),
        ]);
        $strategy = $this->resolveWaitStrategy();
        $strategy->waitUntilReady($instance);
    }
}
