<?php

namespace Tests\Unit\Containers\WaitStrategy;

use Testcontainers\Containers\GenericContainerInstance;
use Testcontainers\Containers\WaitStrategy\HttpProbe;
use Testcontainers\Containers\WaitStrategy\HttpWaitStrategy;

class HttpWaitStrategyTest extends WaitStrategyTestCase
{
    /**
     * @inheritDoc
     */
    public function resolveWaitStrategy()
    {
        return new HttpWaitStrategy();
    }

    public function testWaitUntilReady()
    {
        $instance = new GenericContainerInstance('8188d93d8a27', [
            'ports' => [80 => 8239],
        ]);
        $probe = $this->createMock(HttpProbe::class);
        $probe->method('available')
            ->willReturnOnConsecutiveCalls(false, false, true);
        $strategy = new HttpWaitStrategy($probe);
        $strategy->waitUntilReady($instance);

        $this->assertTrue(true);
    }
}
