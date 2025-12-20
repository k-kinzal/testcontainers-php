<?php

/** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Unit\Containers\PortStrategy;

use PHPUnit\Framework\TestCase;
use Testcontainers\Containers\PortStrategy\AlreadyExistsPortStrategyException;
use Testcontainers\Containers\PortStrategy\ConflictBehavior;
use Testcontainers\Containers\PortStrategy\PortStrategy;
use Testcontainers\Containers\PortStrategy\PortStrategyProvider;

/**
 * @internal
 *
 * @coversNothing
 */
class PortStrategyProviderTest extends TestCase
{
    public function testRegister()
    {
        $provider = new PortStrategyProvider();
        $provider->register('test', new TestPortStrategy());

        $this->assertTrue(true);
    }

    public function testRegisterAlreadyExistsPortStrategyException()
    {
        $this->expectExceptionMessage('Port strategy with name test already exists.');
        $this->expectException(AlreadyExistsPortStrategyException::class);

        $provider = new PortStrategyProvider();
        $provider->register('test', new TestPortStrategy());
        $provider->register('test', new TestPortStrategy());
    }

    public function testGet()
    {
        $strategy = new TestPortStrategy();
        $provider = new PortStrategyProvider();
        $provider->register('test', $strategy);

        $this->assertSame($strategy, $provider->get('test'));
    }

    public function testGetNotFound()
    {
        $provider = new PortStrategyProvider();

        $this->assertNull($provider->get('test'));
    }
}

class TestPortStrategy implements PortStrategy
{
    public function getPort()
    {
        return 0;
    }

    public function conflictBehavior()
    {
        return ConflictBehavior::RETRY();
    }
}
