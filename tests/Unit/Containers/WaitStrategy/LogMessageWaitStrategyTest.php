<?php

namespace Tests\Unit\Containers\WaitStrategy;

use Testcontainers\Containers\GenericContainer\GenericContainerInstance;
use Testcontainers\Containers\WaitStrategy\LogMessageWaitStrategy;
use Testcontainers\Docker\DockerClient;
use Testcontainers\Docker\Output\DockerRunWithDetachOutput;

class LogMessageWaitStrategyTest extends WaitStrategyTestCase
{
    public function resolveWaitStrategy()
    {
        return new LogMessageWaitStrategy();
    }

    public function testWaitUntilReady()
    {
        $client = new DockerClient();

        /** @var DockerRunWithDetachOutput $output */
        $output = $client->run('jpetazzo/clock:latest', null, [], [
            'detach' => true,
        ]);
        $containerId = $output->getContainerId();

        try {
            $instance = new GenericContainerInstance([
                'containerId' => $containerId,
            ]);
            $strategy = (new LogMessageWaitStrategy())
                ->withPattern('\d{2}:\d{2}:\d{2}')
                ->withTimeoutSeconds(5)
            ;
            $strategy->waitUntilReady($instance);
        } finally {
            $client->stop($containerId);
        }

        $this->assertTrue(true);
    }
}
