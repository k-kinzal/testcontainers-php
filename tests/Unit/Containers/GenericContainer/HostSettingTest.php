<?php

/** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Unit\Containers\GenericContainer;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Testcontainers\Containers\GenericContainer\GenericContainer;
use Testcontainers\Containers\GenericContainer\HostSetting;
use Testcontainers\Containers\WaitStrategy\LogMessageWaitStrategy;

/**
 * @internal
 * @coversNothing
 */
class HostSettingTest extends TestCase
{
    public function testHasHostSettingTrait()
    {
        $uses = class_uses(GenericContainer::class);

        $this->assertContains(HostSetting::class, $uses);
    }

    public function testStaticExtraHosts()
    {
        $container = (new HostSettingWithExtraHostsContainer('alpine:latest'))
            ->withCommands(['sh', '-c', 'ping -c 1 example.com'])
            ->withWaitStrategy(new LogMessageWaitStrategy())
        ;
        $instance = $container->start();

        $this->assertStringStartsWith('PING example.com (127.0.0.1)', $instance->getOutput());
    }

    public function testStaticHosts()
    {
        $container = (new HostSettingWithHostsContainer('alpine:latest'))
            ->withCommands(['sh', '-c', 'ping -c 1 example.org'])
            ->withWaitStrategy(new LogMessageWaitStrategy())
        ;
        $instance = $container->start();

        $this->assertStringStartsWith('PING example.org (127.0.0.1)', $instance->getOutput());
    }

    public function testStartWithExtraHost()
    {
        $container = (new GenericContainer('alpine:latest'))
            ->withExtraHost('example.com', '127.0.0.1')
            ->withCommands(['sh', '-c', 'ping -c 1 example.com'])
            ->withWaitStrategy(new LogMessageWaitStrategy())
        ;
        $instance = $container->start();

        $this->assertStringStartsWith('PING example.com (127.0.0.1)', $instance->getOutput());
    }

    public function testStartWithInvalidCall()
    {
        $this->expectExceptionMessage('Invalid arguments: withExtraHost(`{"hostname":"example.com","ipAddress":"127.0.0.1"}`, `"192.168.0.1"`)');
        $this->expectException(InvalidArgumentException::class);

        (new GenericContainer('alpine:latest'))
            ->withExtraHost(['hostname' => 'example.com', 'ipAddress' => '127.0.0.1'], '192.168.0.1')
        ;
    }

    public function testStartWithExtraHosts()
    {
        $container = (new GenericContainer('alpine:latest'))
            ->withExtraHosts([
                ['hostname' => 'example.com', 'ipAddress' => '127.0.0.1'],
                ['hostname' => 'example.org', 'ipAddress' => '127.0.0.1'],
            ])
            ->withCommands(['sh', '-c', 'ping -c 1 example.org'])
            ->withWaitStrategy(new LogMessageWaitStrategy())
        ;
        $instance = $container->start();

        $this->assertStringStartsWith('PING example.org (127.0.0.1)', $instance->getOutput());
    }
}

class HostSettingWithExtraHostsContainer extends GenericContainer
{
    public static $EXTRA_HOSTS = [
        'example.com:127.0.0.1',
    ];
}

class HostSettingWithHostsContainer extends GenericContainer
{
    public static $HOSTS = [
        ['hostname' => 'example.org', 'ipAddress' => '127.0.0.1'],
    ];
}
