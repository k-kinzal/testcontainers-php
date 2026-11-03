<?php

/** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Unit\Containers\GenericContainer;

use PHPUnit\Framework\TestCase;
use Testcontainers\Containers\GenericContainer\GenericContainer;
use Testcontainers\Containers\GenericContainer\HostSetting;
use Testcontainers\Containers\WaitStrategy\LogMessageWaitStrategy;
use UnexpectedValueException;

final class HostSettingTest extends TestCase
{
    /**
     * @return void
     */
    public function testHasHostSettingTrait()
    {
        $uses = class_uses(GenericContainer::class);
        self::assertNotFalse($uses);

        $this->assertContains(HostSetting::class, $uses);
    }

    /**
     * @return void
     */
    public function testStaticExtraHosts()
    {
        $container = (new HostSettingWithExtraHostsContainer('alpine:latest'))
            ->withAutoRemoveOnExit(false)
            ->withCommands(['sh', '-c', 'ping -c 1 example.com'])
            ->withWaitStrategy(new LogMessageWaitStrategy())
        ;
        $instance = $container->start();

        $this->assertStringStartsWith('PING example.com (127.0.0.1)', $instance->getOutput());
    }

    /**
     * @return void
     */
    public function testStaticHosts()
    {
        $container = (new HostSettingWithHostsContainer('alpine:latest'))
            ->withAutoRemoveOnExit(false)
            ->withCommands(['sh', '-c', 'ping -c 1 example.org'])
            ->withWaitStrategy(new LogMessageWaitStrategy())
        ;
        $instance = $container->start();

        $this->assertStringStartsWith('PING example.org (127.0.0.1)', $instance->getOutput());
    }

    /**
     * @return void
     */
    public function testStartWithExtraHost()
    {
        $container = (new GenericContainer('alpine:latest'))
            ->withAutoRemoveOnExit(false)
            ->withExtraHost('example.com', '127.0.0.1')
            ->withCommands(['sh', '-c', 'ping -c 1 example.com'])
            ->withWaitStrategy(new LogMessageWaitStrategy())
        ;
        $instance = $container->start();

        $this->assertStringStartsWith('PING example.com (127.0.0.1)', $instance->getOutput());
    }

    /**
     * @return void
     */
    public function testStartWithInvalidCall()
    {
        $this->expectExceptionMessage('Expected null');
        $this->expectException(UnexpectedValueException::class);

        (new GenericContainer('alpine:latest'))
            ->withExtraHost(['hostname' => 'example.com', 'ipAddress' => '127.0.0.1'], '192.168.0.1')
        ;
    }

    /**
     * @return void
     */
    public function testStartWithExtraHosts()
    {
        $container = (new GenericContainer('alpine:latest'))
            ->withAutoRemoveOnExit(false)
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

    /**
     * @dataProvider providerWithExtraHostsInvalidInputs
     * @param bool $extraHosts
     * @return void
     */
    public function testWithExtraHostsRejectsInvalidInputs($extraHosts)
    {
        $container = new GenericContainer('alpine');
        $this->expectException(UnexpectedValueException::class);

        $container->withExtraHosts($extraHosts);
    }

    /**
     * @return array<string, array{bool}>
     */
    public static function providerWithExtraHostsInvalidInputs()
    {
        return [
            'case 1' => [false],
        ];
    }

    /**
     * @return void
     */
    public function testStartRejectsInvalidExtraHosts()
    {
        $container = new HostSettingWithInvalidExtraHostsContainer('alpine');
        $this->expectException(UnexpectedValueException::class);

        $container->start();
    }
}

final class HostSettingWithExtraHostsContainer extends GenericContainer
{
    public static $EXTRA_HOSTS = [
        'example.com:127.0.0.1',
    ];
}

final class HostSettingWithHostsContainer extends GenericContainer
{
    public static $HOSTS = [
        ['hostname' => 'example.org', 'ipAddress' => '127.0.0.1'],
    ];
}

final class HostSettingWithInvalidExtraHostsContainer extends GenericContainer
{
    protected static $EXTRA_HOSTS = false;
}
