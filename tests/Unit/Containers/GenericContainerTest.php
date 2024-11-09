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
            ->withCommand('pwd');
        $instance = $container->start();

        $this->assertSame("/\n", $instance->getOutput());
    }

    public function testStartWithCommands()
    {
        $container = (new GenericContainer('alpine:latest'))
            ->withCommands(['echo', 'Hello, World!']);
        $instance = $container->start();

        $this->assertSame("Hello, World!\n", $instance->getOutput());
    }

    public function testStartWithEnv()
    {
        $container = (new GenericContainer('alpine:latest'))
            ->withEnv('KEY', 'VALUE')
            ->withCommands(['printenv', 'KEY']);
        $instance = $container->start();

        $this->assertSame("VALUE\n", $instance->getOutput());
    }

    public function testStartWithEnvs()
    {
        $container = (new GenericContainer('alpine:latest'))
            ->withEnvs(['KEY1' => 'VALUE1', 'KEY2' => 'VALUE2'])
            ->withCommands(['printenv', 'KEY2']);
        $instance = $container->start();

        $this->assertSame("VALUE2\n", $instance->getOutput());
    }
}
