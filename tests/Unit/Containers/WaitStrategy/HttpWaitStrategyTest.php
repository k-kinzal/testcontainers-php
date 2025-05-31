<?php

namespace Tests\Unit\Containers\WaitStrategy;

use Testcontainers\Containers\GenericContainer\GenericContainerInstance;
use Testcontainers\Containers\WaitStrategy\HttpProbe;
use Testcontainers\Containers\WaitStrategy\HttpWaitStrategy;
use Testcontainers\Docker\Types\ContainerId;

class HttpWaitStrategyTest extends WaitStrategyTestCase
{
    public function resolveWaitStrategy()
    {
        return new HttpWaitStrategy();
    }

    public function testWaitUntilReady()
    {
        $instance = new GenericContainerInstance([
            'containerId' => new ContainerId('8188d93d8a27'),
            'ports' => [80 => 8239],
        ]);
        $probe = $this->createMock(HttpProbe::class);
        $probe->method('available')
            ->willReturnOnConsecutiveCalls(false, false, true)
        ;
        $strategy = new HttpWaitStrategy($probe);
        $strategy->waitUntilReady($instance);

        $this->assertTrue(true);
    }
}
