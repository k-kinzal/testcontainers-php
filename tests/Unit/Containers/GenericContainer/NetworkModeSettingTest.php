<?php

/** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Unit\Containers\GenericContainer;

use PHPUnit\Framework\TestCase;
use Testcontainers\Containers\GenericContainer\GenericContainer;
use Testcontainers\Containers\GenericContainer\NetworkModeSetting;
use Testcontainers\Containers\Types\NetworkMode;

/**
 * @internal
 *
 * @coversNothing
 */
class NetworkModeSettingTest extends TestCase
{
    public function testHasNetworkModeSettingTrait()
    {
        $uses = class_uses(GenericContainer::class);

        $this->assertContains(NetworkModeSetting::class, $uses);
    }

    public function testStaticNetworkMode()
    {
        $container = (new NetworkModeSettingWithStaticNetworkModeContainer('alpine:latest'))
            ->withCommands(['sh', '-c', 'ls /sys/class/net'])
        ;
        $instance = $container->start();

        $this->assertTrue(strpos($instance->getOutput(), 'eth0') === false);
    }

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
}

class NetworkModeSettingWithStaticNetworkModeContainer extends GenericContainer
{
    protected static $AUTO_REMOVE_ON_EXIT = false;

    protected static $NETWORK_MODE = 'none';
}
