<?php

/** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Unit\Containers\GenericContainer;

use PHPUnit\Framework\TestCase;
use Testcontainers\Containers\GenericContainer\GenericContainer;
use Testcontainers\Containers\GenericContainer\StartupSetting;
use Testcontainers\Containers\StartupCheckStrategy\IsRunningStartupCheckStrategy;
use Testcontainers\Containers\StartupCheckStrategy\StartupCheckFailedException;

/**
 * @internal
 *
 * @coversNothing
 */
class StartupSettingTest extends TestCase
{
    public function testHasStartupSettingTrait()
    {
        $uses = class_uses(GenericContainer::class);

        $this->assertContains(StartupSetting::class, $uses);
    }

    public function testStaticStartupTimeout()
    {
        $this->expectException(StartupCheckFailedException::class);

        $container = (new StartupSettingWithStaticStartupTimeoutContainer('alpine:latest'))
            ->withCommands(['sh', '-c', 'exit 1'])
        ;
        $container->start();
    }

    public function testStaticStartupCheckStrategy()
    {
        $this->expectException(StartupCheckFailedException::class);

        $container = (new StartupSettingWithStaticStartupCheckStrategyContainer('alpine:latest'))
            ->withCommands(['sh', '-c', 'exit 1'])
        ;
        $container->start();
    }

    public function testStartWithStartupTimeout()
    {
        $this->expectException(StartupCheckFailedException::class);

        $container = (new GenericContainer('alpine:latest'))
            ->withStartupTimeout(1)
            ->withCommands(['sh', '-c', 'exit 1'])
        ;
        $container->start();
    }

    public function testStartWithStartupCheckStrategy()
    {
        $this->expectException(StartupCheckFailedException::class);

        $container = (new GenericContainer('alpine:latest'))
            ->withStartupCheckStrategy(new IsRunningStartupCheckStrategy())
            ->withCommands(['sh', '-c', 'exit 1'])
        ;
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
