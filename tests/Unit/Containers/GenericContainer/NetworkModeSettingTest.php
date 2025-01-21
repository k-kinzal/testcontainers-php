<?php

/** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Unit\Containers\GenericContainer;

use PHPUnit\Framework\TestCase;
use Testcontainers\Containers\GenericContainer\GenericContainer;
use Testcontainers\Containers\GenericContainer\NetworkModeSetting;
use Testcontainers\Containers\Types\NetworkMode;

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
            ->withCommands(['sh', '-c', 'ls /sys/class/net']);
        $instance = $container->start();

        $this->assertFalse(strpos($instance->getOutput(), 'eth0'));
    }

    public function testStartWithNetworkMode()
    {
        $container = (new GenericContainer('alpine:latest'))
            ->withNetworkMode(NetworkMode::NONE())
            ->withCommands(['sh', '-c', 'ls /sys/class/net']);
        $instance = $container->start();

        $this->assertFalse(strpos($instance->getOutput(), 'eth0'));
    }

//    public function testStartWithNetworkMode()
//    {
//        $container = (new GenericContainer('alpine:latest'))
//            ->withNetworkMode('none')
//            ->withCommands(['sh', '-c', 'ls /sys/class/net']);
//        /** @noinspection PhpUnhandledExceptionInspection */
//        $instance = $container->start();
//
//        $this->assertFalse(strpos($instance->getOutput(), 'eth0'));
//    }
}

class NetworkModeSettingWithStaticNetworkModeContainer extends GenericContainer
{
    protected static $NETWORK_MODE = 'none';
}
