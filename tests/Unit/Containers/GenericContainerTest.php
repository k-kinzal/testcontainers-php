<?php

/** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Unit\Containers;

use PHPUnit\Framework\TestCase;
use Testcontainers\Containers\ContainerInstance;
use Testcontainers\Containers\GenericContainer\GenericContainer;
use Testcontainers\Containers\PortStrategy\StaticPortStrategy;
use Testcontainers\Docker\Exception\PortAlreadyAllocatedException;

class GenericContainerTest extends TestCase
{
    public function testStart()
    {
        $container = new GenericContainer('alpine:latest');
        $instance = $container->start();

        $this->assertInstanceOf(ContainerInstance::class, $instance);
        $this->assertNotEmpty($instance->getContainerId());
    }

    public function testContainerFailsOnPortConflictWithStaticPortStrategy()
    {
        $this->expectException(PortAlreadyAllocatedException::class);

        $container1 = (new GenericContainer('alpine:latest'))
            ->withExposedPort(80)
            ->withPortStrategy(new StaticPortStrategy(50000))
            ->withCommands(['tail', '-f', '/dev/null'])
        ;

        $instance1 = $container1->start();

        $this->assertInstanceOf(ContainerInstance::class, $instance1);
        $this->assertEquals(50000, $instance1->getMappedPort(80));

        $container2 = (new GenericContainer('alpine:latest'))
            ->withExposedPort(80)
            ->withPortStrategy(new StaticPortStrategy(50000))
        ;

        $container2->start();
    }
}
