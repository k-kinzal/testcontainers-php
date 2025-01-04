<?php

namespace Tests\Unit\Containers\WaitStrategy;

use Testcontainers\Containers\GenericContainer\GenericContainerInstance;
use Testcontainers\Containers\WaitStrategy\HostPortWaitStrategy;
use Testcontainers\Containers\WaitStrategy\PortProbe;
use Testcontainers\Containers\WaitStrategy\WaitingTimeoutException;

class HostPortWaitStrategyTest extends WaitStrategyTestCase
{
    /**
     * @inheritDoc
     */
    public function resolveWaitStrategy()
    {
        return new HostPortWaitStrategy();
    }

    public function testWaitUntilReady()
    {
        $instance = new GenericContainerInstance('8188d93d8a27', [
            'ports' => [80 => 8239],
        ]);
        $probe = $this->createMock(PortProbe::class);
        $probe->method('available')
            ->willReturnOnConsecutiveCalls(false, false, true);
        $strategy = new HostPortWaitStrategy($probe);
        $strategy->waitUntilReady($instance);

        $this->assertTrue(true);
    }

    public function testWaitUntilReadyThrowsWaitingTimeoutException()
    {
        $this->expectException(WaitingTimeoutException::class);

        $instance = new GenericContainerInstance('8188d93d8a27', [
            'ports' => [80 => 8239],
        ]);
        $strategy = (new HostPortWaitStrategy())->withTimeoutSeconds(0);

        $strategy->waitUntilReady($instance);
    }
}
