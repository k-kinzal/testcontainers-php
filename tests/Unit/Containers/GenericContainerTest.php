<?php

namespace Tests\Unit\Containers;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Exception\ProcessTimedOutException;
use Testcontainers\Containers\BindMode;
use Testcontainers\Containers\ContainerInstance;
use Testcontainers\Containers\GenericContainer;
use Testcontainers\Containers\ImagePullPolicy;
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
        $instance = $container->start();

        $this->assertInstanceOf(ContainerInstance::class, $instance);
        $this->assertNotEmpty($instance->getContainerId());
    }

    public function testStartWithFileSystemBind()
    {
        $fp = tmpfile();
        fwrite($fp, 'Hello, World!');
        $meta = stream_get_meta_data($fp);
        $path = $meta['uri'];

        $container = (new GenericContainer('alpine:latest'))
            ->withFileSystemBind($path, '/tmp/test', BindMode::READ_WRITE())
            ->withCommands(['cat', '/tmp/test'])
            ->withWaitStrategy(new LogMessageWaitStrategy());
        $instance = $container->start();

        $this->assertSame("Hello, World!", $instance->getOutput());
    }

    public function testStartWithVolumesFrom()
    {
        $fp = tmpfile();
        fwrite($fp, 'Hello, World!');
        $meta = stream_get_meta_data($fp);
        $path = $meta['uri'];

        $container = (new GenericContainer('alpine:latest'))
            ->withFileSystemBind($path, '/tmp/test', BindMode::READ_WRITE());
        $instance1 = $container->start();

        $container = (new GenericContainer('alpine:latest'))
            ->withVolumesFrom($instance1, BindMode::READ_ONLY())
            ->withCommands(['cat', '/tmp/test'])
            ->withWaitStrategy(new LogMessageWaitStrategy());
        $instance2 = $container->start();

        $this->assertSame("Hello, World!", $instance2->getOutput());
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
            ->withCommands(['echo', 'Hello, World!'])
            ->withWaitStrategy(new LogMessageWaitStrategy());
        $instance = $container->start();

        $this->assertSame("Hello, World!\n", $instance->getOutput());
    }

    public function testStartWithExtraHost()
    {
        $container = (new GenericContainer('alpine:latest'))
            ->withExtraHost('example.com', '127.0.0.1')
            ->withCommands(['sh', '-c', 'ping -c 1 example.com']);
        $instance = $container->start();

        $this->assertStringStartsWith('PING example.com (127.0.0.1)', $instance->getOutput());
    }

    public function testStartWithNetworkMode()
    {
        $container = (new GenericContainer('alpine:latest'))
            ->withNetworkMode('none')
            ->withCommands(['sh', '-c', 'ls /sys/class/net']);
        $instance = $container->start();

        $this->assertFalse(strpos($instance->getOutput(), 'eth0'));
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
        $instance = $container->start();

        $this->assertStringStartsWith('PING my-alias', $instance->getOutput());
    }

    public function testStartWithEnv()
    {
        $container = (new GenericContainer('alpine:latest'))
            ->withEnv('KEY', 'VALUE')
            ->withCommands(['printenv', 'KEY'])
            ->withWaitStrategy(new LogMessageWaitStrategy());
        $instance = $container->start();

        $this->assertSame("VALUE\n", $instance->getOutput());
    }

    public function testStartWithEnvs()
    {
        $container = (new GenericContainer('alpine:latest'))
            ->withEnvs(['KEY1' => 'VALUE1', 'KEY2' => 'VALUE2'])
            ->withCommands(['printenv', 'KEY2'])
            ->withWaitStrategy(new LogMessageWaitStrategy());
        $instance = $container->start();

        $this->assertSame("VALUE2\n", $instance->getOutput());
    }

    public function testStartWithLabels()
    {
        $container = (new GenericContainer('alpine:latest'))
            ->withLabels(['KEY1' => 'VALUE1', 'KEY2' => 'VALUE2'])
            ->withWaitStrategy(new LogMessageWaitStrategy());
        $instance = $container->start();

        $this->assertSame("VALUE1", $instance->getLabel('KEY1'));
        $this->assertSame("VALUE2", $instance->getLabel('KEY2'));
        $this->assertSame(['KEY1' => 'VALUE1', 'KEY2' => 'VALUE2'], $instance->getLabels());
    }

    public function testStartWithExposedPorts()
    {
        $container = (new GenericContainer('alpine:latest'))
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
        $container = (new GenericContainer('alpine:latest'))
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

    public function testStartWithImagePullPolicy()
    {
        $container = (new GenericContainer('alpine:latest'))
            ->withImagePullPolicy(ImagePullPolicy::MISSING());
        $instance = $container->start();

        $this->assertSame(ImagePullPolicy::$MISSING, $instance->getImagePullPolicy()->toString());
    }

    public function testStartWithWorkingDirectory()
    {
        $container = (new GenericContainer('alpine:latest'))
            ->withWorkingDirectory('/tmp')
            ->withCommands(['pwd'])
            ->withWaitStrategy(new LogMessageWaitStrategy());
        $instance = $container->start();

        $this->assertSame("/tmp\n", $instance->getOutput());
    }

    public function testStartWithStartupTimeout()
    {
        $this->expectException(ProcessTimedOutException::class);

        $container = (new GenericContainer('alpine:latest'))
            ->withStartupTimeout(1)
            ->withCommands(['sleep', '5']);
        $instance = $container->start();
    }

    public function testStartWithPrivilegedMode()
    {
        $container = (new GenericContainer('alpine:latest'))
            ->withPrivilegedMode(true);
        $instance = $container->start();

        $this->assertSame(true, $instance->getPrivilegedMode());
    }
}
