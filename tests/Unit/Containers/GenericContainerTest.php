<?php

namespace Tests\Unit\Containers;

use PHPUnit\Framework\TestCase;
use Testcontainers\Containers\ContainerInstance;
use Testcontainers\Containers\GenericContainer;
use Testcontainers\Containers\PortStrategy\LocalRandomPortStrategy;
use Testcontainers\Containers\WaitStrategy\LogMessageWaitStrategy;

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

        while ($instance->isRunning()) {
            usleep(100);
        }

        $this->assertSame("/\n", $instance->getOutput());
    }

    public function testStartWithCommands()
    {
        $container = (new GenericContainer('alpine:latest'))
            ->withCommands(['echo', 'Hello, World!']);
        $instance = $container->start();

        while ($instance->isRunning()) {
            usleep(100);
        }

        $this->assertSame("Hello, World!\n", $instance->getOutput());
    }

    public function testStartWithEnv()
    {
        $container = (new GenericContainer('alpine:latest'))
            ->withEnv('KEY', 'VALUE')
            ->withCommands(['printenv', 'KEY']);
        $instance = $container->start();

        while ($instance->isRunning()) {
            usleep(100);
        }

        $this->assertSame("VALUE\n", $instance->getOutput());
    }

    public function testStartWithEnvs()
    {
        $container = (new GenericContainer('alpine:latest'))
            ->withEnvs(['KEY1' => 'VALUE1', 'KEY2' => 'VALUE2'])
            ->withCommands(['printenv', 'KEY2']);
        $instance = $container->start();

        while ($instance->isRunning()) {
            usleep(100);
        }

        $this->assertSame("VALUE2\n", $instance->getOutput());
    }

    public function testStartWithLabels()
    {
        $container = (new GenericContainer('alpine:latest'))
            ->withLabels(['KEY1' => 'VALUE1', 'KEY2' => 'VALUE2'])
            ->withCommands(['echo', 'Hello, World!']);
        $instance = $container->start();

        while ($instance->isRunning()) {
            usleep(100);
        }

        $this->assertSame("Hello, World!\n", $instance->getOutput());
    }

    public function testStartWithExposedPorts()
    {
        $container = (new GenericContainer('nginx:latest'))
            ->withExposedPorts(80)
            ->withPortStrategy(new LocalRandomPortStrategy());
        $instance = $container->start();

        $this->assertInstanceOf(ContainerInstance::class, $instance);
        $this->assertTrue(is_int($instance->getMappedPort(80)));
        $this->assertGreaterThanOrEqual(49152, $instance->getMappedPort(80));
        $this->assertLessThanOrEqual(65535, $instance->getMappedPort(80));
    }

    public function testStartWithExposedPortsMultiple()
    {
        $container = (new GenericContainer('nginx:latest'))
            ->withExposedPorts([80, 443])
            ->withPortStrategy(new LocalRandomPortStrategy());
        $instance = $container->start();

        $this->assertInstanceOf(ContainerInstance::class, $instance);
        $this->assertTrue(is_int($instance->getMappedPort(80)));
        $this->assertGreaterThanOrEqual(49152, $instance->getMappedPort(80));
        $this->assertLessThanOrEqual(65535, $instance->getMappedPort(80));
        $this->assertTrue(is_int($instance->getMappedPort(443)));
        $this->assertGreaterThanOrEqual(49152, $instance->getMappedPort(443));
        $this->assertLessThanOrEqual(65535, $instance->getMappedPort(443));
    }

    public function testStartWithWorkingDirectory()
    {
        $container = (new GenericContainer('alpine:latest'))
            ->withWorkingDirectory('/tmp')
            ->withCommands(['pwd']);
        $instance = $container->start();

        while ($instance->isRunning()) {
            usleep(100);
        }

        $this->assertSame("/tmp\n", $instance->getOutput());
    }

    public function testStartWithPrivilegedMode()
    {
        $container = (new GenericContainer('alpine:latest'))
            ->withPrivilegedMode(true)
            ->withCommands(['tail', '-f', '/dev/null']);
        $instance = $container->start();

        $this->assertSame(true, $instance->getPrivilegedMode());
    }
}
