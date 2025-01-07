<?php

/** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Unit\Containers\GenericContainer;

use PHPUnit\Framework\TestCase;
use Testcontainers\Containers\BindMode;
use Testcontainers\Containers\GenericContainer\GenericContainer;
use Testcontainers\Containers\GenericContainer\MountSetting;
use Testcontainers\Containers\WaitStrategy\LogMessageWaitStrategy;
use Testcontainers\Docker\DockerClientFactory;

class MountSettingTest extends TestCase
{
    public function testHasMountSettingTrait()
    {
        $uses = class_uses(GenericContainer::class);

        $this->assertContains(MountSetting::class, $uses);
    }

    public function testStaticMounts()
    {
        $tmpdir = sys_get_temp_dir();
        file_put_contents($tmpdir . '/to', 'Hello, World!');

        $client = DockerClientFactory::create()->withEnv([
            'HOST_DIR' => $tmpdir,
        ]);

        $container = (new MountSettingWithMountsContainer('alpine:latest'))
            ->withDockerClient($client)
            ->withCommands(['cat', '/container/path/to'])
            ->withWaitStrategy(new LogMessageWaitStrategy());
        $instance = $container->start();


        $this->assertSame('Hello, World!', $instance->getOutput());
    }

    public function testStaticVolumes()
    {
        $tmpdir = sys_get_temp_dir();
        file_put_contents($tmpdir . '/to', 'Hello, World!');

        $client = DockerClientFactory::create()->withEnv([
            'HOST_DIR' => $tmpdir,
        ]);

        $container = (new MountSettingWithVolumesContainer('alpine:latest'))
            ->withDockerClient($client)
            ->withCommands(['cat', '/container/path/to'])
            ->withWaitStrategy(new LogMessageWaitStrategy());
        $instance = $container->start();

        $this->assertSame('Hello, World!', $instance->getOutput());
    }

    public function testStartWithFileSystemBind()
    {
        $tmpdir = sys_get_temp_dir();
        file_put_contents($tmpdir . '/to', 'Hello, World!');

        $container = (new GenericContainer('alpine:latest'))
            ->withFileSystemBind("$tmpdir/to", '/container/path/to', BindMode::READ_ONLY())
            ->withCommands(['cat', '/container/path/to'])
            ->withWaitStrategy(new LogMessageWaitStrategy());
        $instance = $container->start();

        $this->assertSame('Hello, World!', $instance->getOutput());
    }

    public function testStartWithVolume()
    {
        $tmpdir = sys_get_temp_dir();
        file_put_contents($tmpdir . '/to', 'Hello, World!');

        $container = (new GenericContainer('alpine:latest'))
            ->withVolume("$tmpdir/to:/container/path/to:ro")
            ->withCommands(['cat', '/container/path/to'])
            ->withWaitStrategy(new LogMessageWaitStrategy());
        $instance = $container->start();

        $this->assertSame('Hello, World!', $instance->getOutput());
    }

    public function testStartWithMount()
    {
        $tmpdir = sys_get_temp_dir();
        file_put_contents($tmpdir . '/to', 'Hello, World!');

        $container = (new GenericContainer('alpine:latest'))
            ->withMount([
                'type' => 'bind',
                'source' => "$tmpdir/to",
                'destination' => '/container/path/to',
                'readonly' => true,
            ])
            ->withCommands(['cat', '/container/path/to'])
            ->withWaitStrategy(new LogMessageWaitStrategy());
        $instance = $container->start();

        $this->assertSame('Hello, World!', $instance->getOutput());
    }
}

class MountSettingWithMountsContainer extends GenericContainer
{
    public static $MOUNTS = [
        'type=bind,source=${HOST_DIR},target=/container/path,readonly',
    ];
}

class MountSettingWithVolumesContainer extends GenericContainer
{
    public static $VOLUMES = [
        '${HOST_DIR}:/container/path:ro',
    ];
}