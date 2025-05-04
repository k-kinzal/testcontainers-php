<?php

/** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Unit\Containers\WaitStrategy;

use PHPUnit\Framework\TestCase;
use Testcontainers\Containers\WaitStrategy\AlreadyExistsWaitStrategyException;
use Testcontainers\Containers\WaitStrategy\WaitStrategy;
use Testcontainers\Containers\WaitStrategy\WaitStrategyProvider;

/**
 * @internal
 * @coversNothing
 */
class WaitStrategyProviderTest extends TestCase
{
    public function testRegister()
    {
        $provider = new WaitStrategyProvider();
        $provider->register('test', new TestWaitStrategy());

        $this->assertTrue(true);
    }

    public function testRegisterAlreadyExistsWaitStrategyException()
    {
        $this->expectExceptionMessage('Wait strategy with name test already exists.');
        $this->expectException(AlreadyExistsWaitStrategyException::class);

        $provider = new WaitStrategyProvider();
        $provider->register('test', new TestWaitStrategy());
        $provider->register('test', new TestWaitStrategy());
    }

    public function testGet()
    {
        $strategy = new TestWaitStrategy();
        $provider = new WaitStrategyProvider();
        $provider->register('test', $strategy);

        $this->assertSame($strategy, $provider->get('test'));
    }

    public function testGetNotFound()
    {
        $provider = new WaitStrategyProvider();

        $this->assertNull($provider->get('test'));
    }
}

class TestWaitStrategy implements WaitStrategy
{
    public function waitUntilReady($instance)
    {
    }
}
