<?php

/** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Unit\Containers\WaitStrategy;

use PHPUnit\Framework\TestCase;
use Testcontainers\Containers\WaitStrategy\AlreadyExistsWaitStrategyException;
use Testcontainers\Containers\WaitStrategy\WaitStrategy;
use Testcontainers\Containers\WaitStrategy\WaitStrategyProvider;
use Testcontainers\Logger\WithLogger;

/**
 * @internal
 *
 * @coversNothing
 */
final class WaitStrategyProviderTest extends TestCase
{
    /**
     * @return void
     */
    public function testRegister()
    {
        $provider = new WaitStrategyProvider();
        $provider->register('test', new TestWaitStrategy());

        $this->assertTrue(true);
    }

    /**
     * @return void
     */
    public function testRegisterAlreadyExistsWaitStrategyException()
    {
        $this->expectExceptionMessage('Wait strategy with name test already exists.');
        $this->expectException(AlreadyExistsWaitStrategyException::class);

        $provider = new WaitStrategyProvider();
        $provider->register('test', new TestWaitStrategy());
        $provider->register('test', new TestWaitStrategy());
    }

    /**
     * @return void
     */
    public function testGet()
    {
        $strategy = new TestWaitStrategy();
        $provider = new WaitStrategyProvider();
        $provider->register('test', $strategy);

        $this->assertSame($strategy, $provider->get('test'));
    }

    /**
     * @return void
     */
    public function testGetNotFound()
    {
        $provider = new WaitStrategyProvider();

        $this->assertNull($provider->get('test'));
    }
}

final class TestWaitStrategy implements WaitStrategy
{
    use WithLogger;

    public function waitUntilReady($instance)
    {
    }
}
