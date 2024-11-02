<?php

namespace Tests\Unit\Containers;

use PHPUnit\Framework\TestCase;
use Testcontainers\Containers\ContainerInstance;
use Testcontainers\Containers\GenericContainer;

class GenericContainerTest extends TestCase
{
    public function testStart()
    {
        $container = new GenericContainer('alpine:latest');
        $instance = $container->start();

        $this->assertInstanceOf(ContainerInstance::class, $instance);
        $this->assertNotEmpty($instance->getContainerId());
    }

    public function testStartWithCommand()
    {
        $container = (new GenericContainer('alpine:latest'))
            ->withCommand('ls');
        $instance = $container->start();

        $this->assertInstanceOf(ContainerInstance::class, $instance);
        $this->assertNotEmpty($instance->getContainerId());
    }

    public function testStartWithCommands()
    {
        $container = (new GenericContainer('alpine:latest'))
            ->withCommands(['ls', '-la']);
        $instance = $container->start();

        $this->assertInstanceOf(ContainerInstance::class, $instance);
        $this->assertNotEmpty($instance->getContainerId());
    }
}
