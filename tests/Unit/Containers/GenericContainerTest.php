<?php

namespace Tests\Unit\Containers;

use PHPUnit\Framework\TestCase;
use Testcontainers\Containers\ContainerInstance;
use Testcontainers\Containers\GenericContainer\GenericContainer;
use Testcontainers\Containers\PortStrategy\LocalRandomPortStrategy;
use Testcontainers\Containers\WaitStrategy\LogMessageWaitStrategy;
use Testcontainers\Docker\DockerClientFactory;
use Testcontainers\Testcontainers;
use Tests\Images\DinD;

class GenericContainerTest extends TestCase
{
    public function testStart()
    {
        $container = new GenericContainer('alpine:latest');
        /** @noinspection PhpUnhandledExceptionInspection */
        $instance = $container->start();

        $this->assertInstanceOf(ContainerInstance::class, $instance);
        $this->assertNotEmpty($instance->getContainerId());
    }

    public function testStartWithCommand()
    {
        $container = (new GenericContainer('alpine:latest'))
            ->withCommand('pwd');
        /** @noinspection PhpUnhandledExceptionInspection */
        $instance = $container->start();

        $this->assertSame("/\n", $instance->getOutput());
    }

    public function testStartWithCommands()
    {
        $container = (new GenericContainer('alpine:latest'))
            ->withCommands(['echo', 'Hello, World!'])
            ->withWaitStrategy(new LogMessageWaitStrategy());
        /** @noinspection PhpUnhandledExceptionInspection */
        $instance = $container->start();

        $this->assertSame("Hello, World!\n", $instance->getOutput());
    }

    public function testStartWithNetworkAliases()
    {
        $instance = Testcontainers::run(DinD::class);

        $client = DockerClientFactory::create([
            'globalOptions' => [
                'host' => 'tcp://' . $instance->getHost() . ':' . $instance->getMappedPort(2375)
            ],
        ]);
        $network = md5(uniqid());
        $client->networkCreate($network);

        $container = (new GenericContainer('alpine:latest'))
            ->withDockerClient($client)
            ->withNetworkMode($network)
            ->withNetworkAliases(['my-alias'])
            ->withCommands(['sh', '-c', 'ping -c 1 my-alias']);
        /** @noinspection PhpUnhandledExceptionInspection */
        $instance = $container->start();

        $this->assertStringStartsWith('PING my-alias', $instance->getOutput());
    }

    public function testStartWithExposedPorts()
    {
        $container = (new GenericContainer('alpine:latest'))
            ->withExposedPorts(80)
            ->withPortStrategy(new LocalRandomPortStrategy());
        /** @noinspection PhpUnhandledExceptionInspection */
        $instance = $container->start();

        $this->assertInstanceOf(ContainerInstance::class, $instance);
        $this->assertTrue(is_int($instance->getMappedPort(80)));
        $this->assertGreaterThanOrEqual(49152, $instance->getMappedPort(80));
        $this->assertLessThanOrEqual(65535, $instance->getMappedPort(80));
    }

    public function testStartWithExposedPortsMultiple()
    {
        $container = (new GenericContainer('alpine:latest'))
            ->withExposedPorts([80, 443])
            ->withPortStrategy(new LocalRandomPortStrategy());
        /** @noinspection PhpUnhandledExceptionInspection */
        $instance = $container->start();

        $this->assertInstanceOf(ContainerInstance::class, $instance);
        $this->assertTrue(is_int($instance->getMappedPort(80)));
        $this->assertGreaterThanOrEqual(49152, $instance->getMappedPort(80));
        $this->assertLessThanOrEqual(65535, $instance->getMappedPort(80));
        $this->assertTrue(is_int($instance->getMappedPort(443)));
        $this->assertGreaterThanOrEqual(49152, $instance->getMappedPort(443));
        $this->assertLessThanOrEqual(65535, $instance->getMappedPort(443));
    }
}
