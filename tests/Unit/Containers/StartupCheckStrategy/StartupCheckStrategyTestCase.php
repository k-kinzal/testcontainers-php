<?php

namespace Tests\Unit\Containers\StartupCheckStrategy;

use PHPUnit\Framework\TestCase;
use Testcontainers\Containers\StartupCheckStrategy\StartupCheckStrategy;
use Testcontainers\Docker\DockerClient;

abstract class StartupCheckStrategyTestCase extends TestCase
{
    /**
     * @return StartupCheckStrategy
     */
    abstract public function resolveStartupCheckStrategy();

    public function testWaitUntilStartupSuccessful()
    {
        $strategy = $this->resolveStartupCheckStrategy();

        $client = new DockerClient();
        $output = $client->run('alpine:latest', null, [], [
            'detach' => true,
        ]);
        $containerId = $output->getContainerId();

        $this->assertTrue($strategy->waitUntilStartupSuccessful($containerId));
    }
}
