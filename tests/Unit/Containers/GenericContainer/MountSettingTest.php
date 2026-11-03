<?php

/** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Unit\Containers\GenericContainer;

use PHPUnit\Framework\TestCase;
use Testcontainers\Containers\GenericContainer\GenericContainer;
use Testcontainers\Containers\GenericContainer\MountSetting;
use Testcontainers\Containers\Types\BindMode;
use Testcontainers\Containers\Types\Mount;
use Testcontainers\Containers\WaitStrategy\LogMessageWaitStrategy;
use Testcontainers\Docker\DockerClientFactory;
use UnexpectedValueException;

final class MountSettingTest extends TestCase
{
    /**
     * @return void
     */
    public function testMountsIsProvidedByMountSettingTrait()
    {
        $uses = class_uses(GenericContainer::class);
        self::assertNotFalse($uses);
        self::assertContains(MountSetting::class, $uses);
    }

    /**
     * @return void
     */
    public function testMountsFromStaticMounts()
    {
        $tmpdir = sys_get_temp_dir();
        file_put_contents($tmpdir.'/to', 'Hello, World!');

        $client = DockerClientFactory::create()->withEnv([
            'HOST_DIR' => $tmpdir,
        ]);

        $container = (new MountSettingWithMountsContainer('alpine:latest'))
            ->withDockerClient($client)
            ->withCommands(['cat', '/container/path/to'])
            ->withWaitStrategy(new LogMessageWaitStrategy())
        ;
        $instance = $container->start();

        self::assertSame('Hello, World!', $instance->getOutput());
    }

    /**
     * @return void
     */
    public function testMountsFromStaticVolumes()
    {
        $tmpdir = sys_get_temp_dir();
        file_put_contents($tmpdir.'/to', 'Hello, World!');

        $client = DockerClientFactory::create()->withEnv([
            'HOST_DIR' => $tmpdir,
        ]);

        $container = (new MountSettingWithVolumesContainer('alpine:latest'))
            ->withDockerClient($client)
            ->withCommands(['cat', '/container/path/to'])
            ->withWaitStrategy(new LogMessageWaitStrategy())
        ;
        $instance = $container->start();

        self::assertSame('Hello, World!', $instance->getOutput());
    }

    /**
     * @return void
     */
    public function testWithFileSystemBind()
    {
        $tmpdir = sys_get_temp_dir();
        file_put_contents($tmpdir.'/to', 'Hello, World!');

        $container = (new GenericContainer('alpine:latest'))
            ->withAutoRemoveOnExit(false)
            ->withFileSystemBind("{$tmpdir}/to", '/container/path/to', BindMode::READ_ONLY())
            ->withCommands(['cat', '/container/path/to'])
            ->withWaitStrategy(new LogMessageWaitStrategy())
        ;
        $instance = $container->start();

        self::assertSame('Hello, World!', $instance->getOutput());
    }

    /**
     * @return void
     */
    public function testWithFileSystemBinds()
    {
        $tmpdir = sys_get_temp_dir();
        file_put_contents($tmpdir.'/to', 'Hello, World!');

        $container = (new GenericContainer('alpine:latest'))
            ->withAutoRemoveOnExit(false)
            ->withFileSystemBinds([
                "{$tmpdir}/to:/container/path/to:ro",
            ])
            ->withCommands(['cat', '/container/path/to'])
            ->withWaitStrategy(new LogMessageWaitStrategy())
        ;
        $instance = $container->start();

        self::assertSame('Hello, World!', $instance->getOutput());
    }

    /**
     * @return void
     */
    public function testWithVolume()
    {
        $tmpdir = sys_get_temp_dir();
        file_put_contents($tmpdir.'/to', 'Hello, World!');

        $container = (new GenericContainer('alpine:latest'))
            ->withAutoRemoveOnExit(false)
            ->withVolume("{$tmpdir}/to:/container/path/to:ro")
            ->withCommands(['cat', '/container/path/to'])
            ->withWaitStrategy(new LogMessageWaitStrategy())
        ;
        $instance = $container->start();

        self::assertSame('Hello, World!', $instance->getOutput());
    }

    /**
     * @return void
     */
    public function testWithVolumes()
    {
        $tmpdir = sys_get_temp_dir();
        file_put_contents($tmpdir.'/to', 'Hello, World!');

        $container = (new GenericContainer('alpine:latest'))
            ->withAutoRemoveOnExit(false)
            ->withVolumes([
                [
                    'type' => 'bind',
                    'source' => "{$tmpdir}/to",
                    'target' => '/container/path/to',
                    'readonly' => true,
                ],
            ])
            ->withCommands(['cat', '/container/path/to'])
            ->withWaitStrategy(new LogMessageWaitStrategy())
        ;
        $instance = $container->start();

        self::assertSame('Hello, World!', $instance->getOutput());
    }

    /**
     * @return void
     */
    public function testWithMount()
    {
        $tmpdir = sys_get_temp_dir();
        file_put_contents($tmpdir.'/to', 'Hello, World!');

        $container = (new GenericContainer('alpine:latest'))
            ->withAutoRemoveOnExit(false)
            ->withMount([
                'type' => 'bind',
                'source' => "{$tmpdir}/to",
                'destination' => '/container/path/to',
                'readonly' => true,
            ])
            ->withCommands(['cat', '/container/path/to'])
            ->withWaitStrategy(new LogMessageWaitStrategy())
        ;
        $instance = $container->start();

        self::assertSame('Hello, World!', $instance->getOutput());
    }

    /**
     * @return void
     */
    public function testWithMounts()
    {
        $tmpdir = sys_get_temp_dir();
        file_put_contents($tmpdir.'/to', 'Hello, World!');

        $container = (new GenericContainer('alpine:latest'))
            ->withAutoRemoveOnExit(false)
            ->withMounts([
                Mount::fromString("{$tmpdir}/to:/container/path/to:ro"),
            ])
            ->withCommands(['cat', '/container/path/to'])
            ->withWaitStrategy(new LogMessageWaitStrategy())
        ;
        $instance = $container->start();

        self::assertSame('Hello, World!', $instance->getOutput());
    }

    /**
     * @dataProvider providerWithMountsRejectsInvalidInputs
     * @param bool $mounts
     * @return void
     */
    public function testWithMountsRejectsInvalidInputs($mounts)
    {
        $container = new GenericContainer('alpine');
        $this->expectException(UnexpectedValueException::class);

        $container->withMounts($mounts);
    }

    /**
     * @return array<string, array{bool}>
     */
    public static function providerWithMountsRejectsInvalidInputs()
    {
        return [
            'case 1' => [false],
        ];
    }

    /**
     * @return void
     */
    public function testMountsRejectsInvalidStaticConfiguration()
    {
        $container = new MountSettingWithInvalidMountsContainer('alpine');
        $this->expectException(UnexpectedValueException::class);

        $container->start();
    }
}

final class MountSettingWithMountsContainer extends GenericContainer
{
    protected static $AUTO_REMOVE_ON_EXIT = false;

    public static $MOUNTS = [
        'type=bind,source=${HOST_DIR},target=/container/path,readonly',
    ];
}

final class MountSettingWithVolumesContainer extends GenericContainer
{
    protected static $AUTO_REMOVE_ON_EXIT = false;

    public static $VOLUMES = [
        '${HOST_DIR}:/container/path:ro',
    ];
}

final class MountSettingWithInvalidMountsContainer extends GenericContainer
{
    protected static $MOUNTS = false;
}
