<?php

namespace Tests\Unit\Containers\StartupCheckStrategy;

use PHPUnit\Framework\TestCase;
use Testcontainers\Containers\StartupCheckStrategy\AlreadyExistsStartupStrategyException;
use Testcontainers\Containers\StartupCheckStrategy\IsRunningStartupCheckStrategy;
use Testcontainers\Containers\StartupCheckStrategy\StartupCheckStrategyProvider;

class StartupCheckStrategyProviderTest extends TestCase
{
    public function testRegister()
    {
        $provider = new StartupCheckStrategyProvider();
        /** @noinspection PhpUnhandledExceptionInspection */
        $provider->register(new IsRunningStartupCheckStrategy());

        $this->assertTrue(true);
    }

    public function testRegisterAlreadyExistsStartupStrategyException()
    {
        $this->expectExceptionMessage('Startup strategy with name is_running already exists.');
        $this->expectException(AlreadyExistsStartupStrategyException::class);

        $provider = new StartupCheckStrategyProvider();
        /** @noinspection PhpUnhandledExceptionInspection */
        $provider->register(new IsRunningStartupCheckStrategy());
        /** @noinspection PhpUnhandledExceptionInspection */
        $provider->register(new IsRunningStartupCheckStrategy());
    }

    public function testGet()
    {
        $provider = new StartupCheckStrategyProvider();
        /** @noinspection PhpUnhandledExceptionInspection */
        $provider->register(new IsRunningStartupCheckStrategy());

        $this->assertSame(IsRunningStartupCheckStrategy::class, get_class($provider->get('is_running')));
    }
}
