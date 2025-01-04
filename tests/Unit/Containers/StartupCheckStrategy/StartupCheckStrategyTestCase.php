<?php

namespace Tests\Unit\Containers\StartupCheckStrategy;

use PHPUnit\Framework\TestCase;
use Testcontainers\Containers\GenericContainerInstance;
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
        $output = $client->run('alpine:latest', null, null, [
            'detach' => true,
        ]);
        $containerId = $output->getContainerId();

        $this->assertTrue($strategy->waitUntilStartupSuccessful($containerId));
    }

    public function testInterfaceGetName()
    {
        $strategy = $this->resolveStartupCheckStrategy();
        $name = $strategy->getName();

        $this->assertTrue(is_string($name));
        $this->assertNotEmpty($name);
        $this->assertTrue(preg_match('/^[a-z_][a-z0-9_]*$/', $name) === 1);
    }

    public function testInterfaceGetNameConsistency()
    {
        $strategy = $this->resolveStartupCheckStrategy();
        $name1 = $strategy->getName();
        $name2 = $strategy->getName();

        $this->assertSame($name1, $name2);
    }
}
