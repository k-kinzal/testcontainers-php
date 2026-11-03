<?php

/** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Unit\Containers\GenericContainer;

use PHPUnit\Framework\TestCase;
use Testcontainers\Containers\GenericContainer\GenericContainer;
use Testcontainers\Containers\GenericContainer\NetworkModeSetting;
use Testcontainers\Containers\Types\NetworkMode;
use UnexpectedValueException;

final class NetworkModeSettingTest extends TestCase
{
    /**
     * @return void
     */
    public function testHasNetworkModeSettingTrait()
    {
        $uses = class_uses(GenericContainer::class);
        self::assertNotFalse($uses);

        $this->assertContains(NetworkModeSetting::class, $uses);
    }

    /**
     * @return void
     */
    public function testStaticNetworkMode()
    {
        $container = (new NetworkModeSettingWithStaticNetworkModeContainer('alpine:latest'))
            ->withCommands(['sh', '-c', 'ls /sys/class/net'])
        ;
        $instance = $container->start();

        $this->assertTrue(strpos($instance->getOutput(), 'eth0') === false);
    }

    /**
     * @return void
     */
    public function testStartWithNetworkMode()
    {
        $container = (new GenericContainer('alpine:latest'))
            ->withAutoRemoveOnExit(false)
            ->withNetworkMode(NetworkMode::NONE())
            ->withCommands(['sh', '-c', 'ls /sys/class/net'])
        ;
        $instance = $container->start();

        $this->assertTrue(strpos($instance->getOutput(), 'eth0') === false);
    }

    /**
     * @return void
     */
    public function testStartRejectsInvalidNetworkMode()
    {
        $container = new NetworkModeSettingWithInvalidNetworkModeContainer('alpine');
        $this->expectException(UnexpectedValueException::class);

        $container->start();
    }
}

final class NetworkModeSettingWithStaticNetworkModeContainer extends GenericContainer
{
    protected static $AUTO_REMOVE_ON_EXIT = false;

    protected static $NETWORK_MODE = 'none';
}

final class NetworkModeSettingWithInvalidNetworkModeContainer extends GenericContainer
{
    protected static $NETWORK_MODE = [];
}
