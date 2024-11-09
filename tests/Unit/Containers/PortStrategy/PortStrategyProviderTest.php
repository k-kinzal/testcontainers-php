<?php

namespace Tests\Unit\Containers\PortStrategy;

use PHPUnit\Framework\TestCase;
use Testcontainers\Containers\PortStrategy\AlreadyExistsPortStrategyException;
use Testcontainers\Containers\PortStrategy\ConflictBehavior;
use Testcontainers\Containers\PortStrategy\PortStrategy;
use Testcontainers\Containers\PortStrategy\PortStrategyProvider;

class PortStrategyProviderTest extends TestCase
{
    public function testRegister()
    {
        $provider = new PortStrategyProvider();
        /** @noinspection PhpUnhandledExceptionInspection */
        $provider->register(new TestPortStrategy());

        $this->assertTrue(true);
    }

    public function testRegisterAlreadyExistsPortStrategyException()
    {
        $this->expectExceptionMessage('Port strategy with name test already exists.');
        $this->expectException(AlreadyExistsPortStrategyException::class);

        $provider = new PortStrategyProvider();
        /** @noinspection PhpUnhandledExceptionInspection */
        $provider->register(new TestPortStrategy());
        /** @noinspection PhpUnhandledExceptionInspection */
        $provider->register(new TestPortStrategy());
    }

    public function testGet()
    {
        $strategy = new TestPortStrategy();
        $provider = new PortStrategyProvider();
        /** @noinspection PhpUnhandledExceptionInspection */
        $provider->register($strategy);

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

    public function getName()
    {
        return 'test';
    }

    public function conflictBehavior()
    {
        return ConflictBehavior::RETRY();
    }
}
