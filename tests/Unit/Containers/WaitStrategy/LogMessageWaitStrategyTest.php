<?php

namespace Tests\Unit\Containers\WaitStrategy;

use Testcontainers\Containers\GenericContainerInstance;
use Testcontainers\Containers\WaitStrategy\LogMessageWaitStrategy;
use Testcontainers\Docker\DockerClient;

class LogMessageWaitStrategyTest extends WaitStrategyTestCase
{
    /**
     * @inheritDoc
     */
    public function resolveWaitStrategy()
    {
        return new LogMessageWaitStrategy();
    }

    public function testWaitUntilReady()
    {
        $client = new DockerClient();
        $output = $client->run('jpetazzo/clock:latest', null, null, [
            'detach' => true,
        ]);
        $containerId = $output->getContainerId();

        try {
            $instance = new GenericContainerInstance($containerId);
            $strategy = (new LogMessageWaitStrategy())
                ->withPattern('\d{2}:\d{2}:\d{2}')
                ->withTimeoutSeconds(5);
            $strategy->waitUntilReady($instance);
        } finally {
            $client->stop($containerId);
        }

        $this->assertTrue(true);
    }
}
