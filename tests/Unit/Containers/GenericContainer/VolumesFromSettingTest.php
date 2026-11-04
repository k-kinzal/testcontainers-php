<?php

/** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Unit\Containers\GenericContainer;

use PHPUnit\Framework\TestCase;
use Testcontainers\Containers\GenericContainer\GenericContainer;
use Testcontainers\Containers\GenericContainer\VolumesFromSetting;
use Testcontainers\Containers\Types\BindMode;
use Testcontainers\Containers\WaitStrategy\LogMessageWaitStrategy;
use Testcontainers\Docker\DockerClient;
use Testcontainers\Testcontainers;
use Tests\Images\AlpineContainer;
use Tests\Images\DinD;
use UnexpectedValueException;

final class VolumesFromSettingTest extends TestCase
{
    /**
     * @return void
     */
    public function testVolumesFromIsProvidedByVolumesFromSettingTrait()
    {
        $uses = class_uses(GenericContainer::class);
        self::assertNotFalse($uses);
        self::assertContains(VolumesFromSetting::class, $uses);
    }

    /**
     * @return void
     */
    public function testVolumesFromFromStaticVolumesFrom()
    {
        $fp = tmpfile();
        self::assertNotFalse($fp);
        fwrite($fp, "Hello, World!\n");
        $meta = stream_get_meta_data($fp);
        $path = $meta['uri'];

        $dind = Testcontainers::run(DinD::class);
        $client = $dind->getData(DockerClient::class);

        $container = (new AlpineContainer())
            ->withDockerClient($client)
            ->withName('volumes-from-container')
            ->withFileSystemBind($path, $path, BindMode::READ_WRITE())
        ;

        $source = $container->start();

        $container = (new VolumesFromSettingWithStaticVolumesFromContainer('alpine:latest'))
            ->withDockerClient($client)
            ->withAutoRemoveOnExit(true)
            ->withStopTimeout(0)
            ->withCommands(['sh', '-c', 'cat '.escapeshellarg($path).'; sleep 300'])
        ;
        $instance = $container->start();

        self::assertSame('', $instance->getErrorOutput());
        self::assertSame("Hello, World!\n", $instance->getOutput());
        self::assertTrue($source->isRunning());
    }

    /**
     * @return void
     */
    public function testWithVolumesFrom()
    {
        $fp = tmpfile();
        self::assertNotFalse($fp);
        fwrite($fp, "Hello, World!\n");
        $meta = stream_get_meta_data($fp);
        $path = $meta['uri'];

        $container = (new AlpineContainer())
            ->withFileSystemBind($path, '/tmp/test', BindMode::READ_WRITE())
        ;
        $instance1 = $container->start();

        $container = (new GenericContainer('alpine:latest'))
            ->withAutoRemoveOnExit(true)
            ->withStopTimeout(0)
            ->withCommands(['sh', '-c', 'cat /tmp/test; sleep 300'])
            ->withWaitStrategy(new LogMessageWaitStrategy())
            ->withVolumesFrom($instance1, BindMode::READ_ONLY())
        ;
        $instance2 = $container->start();

        self::assertSame("Hello, World!\n", $instance2->getOutput());
    }

    /**
     * @return void
     */
    public function testVolumesFromRejectsInvalidStaticConfiguration()
    {
        $container = new VolumesFromSettingWithInvalidVolumesFromContainer('alpine');
        $this->expectException(UnexpectedValueException::class);

        $container->start();
    }
}

final class VolumesFromSettingWithStaticVolumesFromContainer extends GenericContainer
{
    protected static $VOLUMES_FROM = [
        ['name' => 'volumes-from-container', 'mode' => 'rw'],
    ];
}

final class VolumesFromSettingWithInvalidVolumesFromContainer extends GenericContainer
{
    protected static $VOLUMES_FROM = false;
}
