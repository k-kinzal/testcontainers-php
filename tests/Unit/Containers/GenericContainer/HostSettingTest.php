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
    public function testExtraHostsIsProvidedByHostSettingTrait()
    {
        $uses = class_uses(GenericContainer::class);
        self::assertNotFalse($uses);
        self::assertContains(HostSetting::class, $uses);
    }

    /**
     * @return void
     */
    public function testExtraHostsFromStaticExtraHosts()
    {
        $container = (new HostSettingWithExtraHostsContainer('alpine:latest'))
            ->withAutoRemoveOnExit(true)
            ->withStopTimeout(0)
            ->withCommands(['sh', '-c', 'ping -c 1 example.com; sleep 300'])
            ->withWaitStrategy(new LogMessageWaitStrategy())
        ;
        $instance = $container->start();

        self::assertStringStartsWith('PING example.com (127.0.0.1)', $instance->getOutput());
    }

    /**
     * @return void
     */
    public function testExtraHostsFromStaticHosts()
    {
        $container = (new HostSettingWithHostsContainer('alpine:latest'))
            ->withAutoRemoveOnExit(true)
            ->withStopTimeout(0)
            ->withCommands(['sh', '-c', 'ping -c 1 example.org; sleep 300'])
            ->withWaitStrategy(new LogMessageWaitStrategy())
        ;
        $instance = $container->start();

        self::assertStringStartsWith('PING example.org (127.0.0.1)', $instance->getOutput());
    }

    /**
     * @return void
     */
    public function testWithExtraHost()
    {
        $container = (new GenericContainer('alpine:latest'))
            ->withExtraHost('example.com', '127.0.0.1')
            ->withAutoRemoveOnExit(true)
            ->withStopTimeout(0)
            ->withCommands(['sh', '-c', 'ping -c 1 example.com; sleep 300'])
            ->withWaitStrategy(new LogMessageWaitStrategy())
        ;
        $instance = $container->start();

        self::assertStringStartsWith('PING example.com (127.0.0.1)', $instance->getOutput());
    }

    /**
     * @return void
     */
    public function testWithExtraHostRejectsInvalidCall()
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
    public function testWithExtraHosts()
    {
        $container = (new GenericContainer('alpine:latest'))
            ->withExtraHosts([
                ['hostname' => 'example.com', 'ipAddress' => '127.0.0.1'],
                ['hostname' => 'example.org', 'ipAddress' => '127.0.0.1'],
            ])
            ->withAutoRemoveOnExit(true)
            ->withStopTimeout(0)
            ->withCommands(['sh', '-c', 'ping -c 1 example.org; sleep 300'])
            ->withWaitStrategy(new LogMessageWaitStrategy())
        ;
        $instance = $container->start();

        self::assertStringStartsWith('PING example.org (127.0.0.1)', $instance->getOutput());
    }

    /**
     * @dataProvider providerWithExtraHostsRejectsInvalidInputs
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
    public static function providerWithExtraHostsRejectsInvalidInputs()
    {
        return [
            'case 1' => [false],
        ];
    }

    /**
     * @return void
     */
    public function testExtraHostsRejectsInvalidStaticConfiguration()
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
