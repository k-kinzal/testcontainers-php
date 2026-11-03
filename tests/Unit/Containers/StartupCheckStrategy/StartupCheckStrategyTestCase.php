<?php

namespace Tests\Unit\Containers\StartupCheckStrategy;

use PHPUnit\Framework\TestCase;
use Testcontainers\Containers\GenericContainer\GenericContainerInstance;
use Testcontainers\Containers\StartupCheckStrategy\StartupCheckStrategy;
use Testcontainers\Docker\DockerClient;
use Testcontainers\Docker\Output\DockerRunWithDetachOutput;

abstract class StartupCheckStrategyTestCase extends TestCase
{
    /**
     * @return array<array{StartupCheckStrategy}>
     */
    abstract public static function providerWaitUntilStartupSuccessful();

    /**
     * @dataProvider providerWaitUntilStartupSuccessful
     * @param StartupCheckStrategy $strategy
     * @return void
     */
    public function testWaitUntilStartupSuccessful($strategy)
    {
        $client = new DockerClient();

        /** @var DockerRunWithDetachOutput $output */
        $output = $client->run('alpine:latest', null, [], [
            'detach' => true,
        ]);
        $instance = new GenericContainerInstance([
            'containerId' => $output->getContainerId(),
        ]);

        $this->assertTrue($strategy->waitUntilStartupSuccessful($instance));
    }
}
