<?php

/** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Unit\Containers\StartupCheckStrategy;

use PHPUnit\Framework\TestCase;
use Testcontainers\Containers\StartupCheckStrategy\AlreadyExistsStartupStrategyException;
use Testcontainers\Containers\StartupCheckStrategy\IsRunningStartupCheckStrategy;
use Testcontainers\Containers\StartupCheckStrategy\StartupCheckStrategyProvider;

final class StartupCheckStrategyProviderTest extends TestCase
{
    /**
     * @return void
     */
    public function testRegister()
    {
        $provider = new StartupCheckStrategyProvider();
        $provider->register('is_running', new IsRunningStartupCheckStrategy());

        $this->assertTrue(true);
    }

    /**
     * @return void
     */
    public function testRegisterAlreadyExistsStartupStrategyException()
    {
        $this->expectExceptionMessage('Startup strategy with name is_running already exists.');
        $this->expectException(AlreadyExistsStartupStrategyException::class);

        $provider = new StartupCheckStrategyProvider();
        $provider->register('is_running', new IsRunningStartupCheckStrategy());
        $provider->register('is_running', new IsRunningStartupCheckStrategy());
    }

    /**
     * @return void
     */
    public function testGet()
    {
        $provider = new StartupCheckStrategyProvider();
        $provider->register('is_running', new IsRunningStartupCheckStrategy());

        $strategy = $provider->get('is_running');

        $this->assertNotNull($strategy);
        $this->assertInstanceOf(IsRunningStartupCheckStrategy::class, $strategy);
    }
}
