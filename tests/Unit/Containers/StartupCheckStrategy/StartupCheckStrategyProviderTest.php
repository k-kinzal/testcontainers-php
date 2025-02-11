<?php

/** @noinspection PhpUnhandledExceptionInspection */

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
        $provider->register('is_running', new IsRunningStartupCheckStrategy());

        $this->assertTrue(true);
    }

    public function testRegisterAlreadyExistsStartupStrategyException()
    {
        $this->expectExceptionMessage('Startup strategy with name is_running already exists.');
        $this->expectException(AlreadyExistsStartupStrategyException::class);

        $provider = new StartupCheckStrategyProvider();
        $provider->register('is_running', new IsRunningStartupCheckStrategy());
        $provider->register('is_running', new IsRunningStartupCheckStrategy());
    }

    public function testGet()
    {
        $provider = new StartupCheckStrategyProvider();
        $provider->register('is_running', new IsRunningStartupCheckStrategy());

        $this->assertSame(IsRunningStartupCheckStrategy::class, get_class($provider->get('is_running')));
    }
}
