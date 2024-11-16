<?php

namespace Tests\Unit\Containers\WaitStrategy;

use PHPUnit\Framework\TestCase;
use Testcontainers\Containers\WaitStrategy\AlreadyExistsWaitStrategyException;
use Testcontainers\Containers\WaitStrategy\WaitStrategy;
use Testcontainers\Containers\WaitStrategy\WaitStrategyProvider;

class WaitStrategyProviderTest extends TestCase
{
    public function testRegister()
    {
        $provider = new WaitStrategyProvider();
        /** @noinspection PhpUnhandledExceptionInspection */
        $provider->register(new TestWaitStrategy());

        $this->assertTrue(true);
    }

    public function testRegisterAlreadyExistsWaitStrategyException()
    {
        $this->expectExceptionMessage('Wait strategy with name test already exists.');
        $this->expectException(AlreadyExistsWaitStrategyException::class);

        $provider = new WaitStrategyProvider();
        /** @noinspection PhpUnhandledExceptionInspection */
        $provider->register(new TestWaitStrategy());
        /** @noinspection PhpUnhandledExceptionInspection */
        $provider->register(new TestWaitStrategy());
    }

    public function testGet()
    {
        $strategy = new TestWaitStrategy();
        $provider = new WaitStrategyProvider();
        /** @noinspection PhpUnhandledExceptionInspection */
        $provider->register($strategy);

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

    public function getName()
    {
        return 'test';
    }
}
