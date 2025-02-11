<?php

/** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Unit\Containers\GenericContainer;

use PHPUnit\Framework\TestCase;
use Testcontainers\Containers\BindMode;
use Testcontainers\Containers\GenericContainer\GenericContainer;
use Testcontainers\Containers\GenericContainer\VolumesFromSetting;
use Testcontainers\Containers\WaitStrategy\LogMessageWaitStrategy;
use Testcontainers\Docker\DockerClientFactory;
use Testcontainers\Testcontainers;
use Tests\Images\DinD;

class VolumesFromSettingTest extends TestCase
{
    public function testHasVolumesFromSettingTrait()
    {
        $uses = class_uses(GenericContainer::class);

        $this->assertContains(VolumesFromSetting::class, $uses);
    }

    public function testStaticVolumesFrom()
    {
        $fp = tmpfile();
        fwrite($fp, 'Hello, World!');
        $meta = stream_get_meta_data($fp);
        $path = $meta['uri'];

        $instance = Testcontainers::run(
            (new DinD())
                ->withFileSystemBind(dirname($path), dirname($path), BindMode::READ_WRITE())
        );
        $client = DockerClientFactory::create([
            'globalOptions' => [
                'host' => 'tcp://' . $instance->getHost() . ':' . $instance->getMappedPort(2375)
            ],
        ]);

        $container = (new GenericContainer('alpine:latest'))
            ->withDockerClient($client)
            ->withName('volumes-from-container')
            ->withFileSystemBind($path, $path, BindMode::READ_WRITE());
        /** @noinspection PhpUnusedLocalVariableInspection */
        $instance1 = $container->start();

        $container = (new VolumesFromSettingWithStaticVolumesFromContainer('alpine:latest'))
            ->withDockerClient($client)
            ->withCommands(['cat', $path])
            ->withWaitStrategy(new LogMessageWaitStrategy());
        $instance2 = $container->start();

        $this->assertSame('', $instance2->getErrorOutput());
        $this->assertSame("Hello, World!", $instance2->getOutput());
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
}

class VolumesFromSettingWithStaticVolumesFromContainer extends GenericContainer
{
    protected static $VOLUMES_FROM = [
        ['name' => 'volumes-from-container', 'mode' => 'rw'],
    ];
}
