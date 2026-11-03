<?php

/** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Unit\Containers\WaitStrategy;

use PHPUnit\Framework\TestCase;
use Testcontainers\Containers\WaitStrategy\HostPortWaitStrategy;
use Testcontainers\Containers\WaitStrategy\AlreadyExistsWaitStrategyException;
use Testcontainers\Containers\WaitStrategy\WaitStrategyProvider;

final class WaitStrategyProviderTest extends TestCase
{
    /**
     * @return void
     */
    public function testRegister()
    {
        $provider = new WaitStrategyProvider();
        $provider->register('test', new HostPortWaitStrategy());

        self::assertTrue(true);
    }

    /**
     * @return void
     */
    public function testRegisterAlreadyExistsWaitStrategyException()
    {
        $this->expectExceptionMessage('Wait strategy with name test already exists.');
        $this->expectException(AlreadyExistsWaitStrategyException::class);

        $provider = new WaitStrategyProvider();
        $provider->register('test', new HostPortWaitStrategy());
        $provider->register('test', new HostPortWaitStrategy());
    }

    /**
     * @return void
     */
    public function testGet()
    {
        $strategy = new HostPortWaitStrategy();
        $provider = new WaitStrategyProvider();
        $provider->register('test', $strategy);

        self::assertSame($strategy, $provider->get('test'));
    }

    /**
     * @return void
     */
    public function testGetNotFound()
    {
        $provider = new WaitStrategyProvider();

        self::assertNull($provider->get('test'));
    }
}
