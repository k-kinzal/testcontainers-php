<?php

/** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Unit\Containers\GenericContainer;

use PHPUnit\Framework\TestCase;
use RuntimeException;
use Symfony\Component\Process\Exception\ProcessTimedOutException;
use Testcontainers\Containers\GenericContainer\GenericContainer;
use Testcontainers\Containers\GenericContainer\StartupSetting;
use Testcontainers\Containers\StartupCheckStrategy\IsRunningStartupCheckStrategy;

class StartupSettingTest extends TestCase
{
    public function testHasStartupSettingTrait()
    {
        $uses = class_uses(GenericContainer::class);

        $this->assertContains(StartupSetting::class, $uses);
    }

    public function testStaticStartupTimeout()
    {
        $this->expectException(ProcessTimedOutException::class);

        $container = (new StartupSettingWithStaticStartupTimeoutContainer('alpine:latest'))
            ->withCommands(['sleep', '5']);
        $container->start();
    }

    public function testStaticStartupCheckStrategy()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Illegal state of container');

        $container = (new StartupSettingWithStaticStartupCheckStrategyContainer('alpine:latest'))
            ->withCommands(['sh', '-c', 'exit 1']);
        $container->start();
    }

    public function testStartWithStartupTimeout()
    {
        $this->expectException(ProcessTimedOutException::class);

        $container = (new GenericContainer('alpine:latest'))
            ->withStartupTimeout(1)
            ->withCommands(['sleep', '5']);
        $container->start();
    }

    public function testStartWithStartupCheckStrategy()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Illegal state of container');

        $container = (new GenericContainer('alpine:latest'))
            ->withStartupCheckStrategy(new IsRunningStartupCheckStrategy())
            ->withCommands(['sh', '-c', 'exit 1']);
        $container->start();
    }
}

class StartupSettingWithStaticStartupTimeoutContainer extends GenericContainer
{
    protected static $STARTUP_TIMEOUT = 1;
}

class StartupSettingWithStaticStartupCheckStrategyContainer extends GenericContainer
{
    protected static $STARTUP_CHECK_STRATEGY = 'is_running';
}
