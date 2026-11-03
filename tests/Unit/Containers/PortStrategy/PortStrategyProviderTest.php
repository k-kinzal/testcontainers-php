<?php

/** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Unit\Containers\PortStrategy;

use PHPUnit\Framework\TestCase;
use Testcontainers\Containers\PortStrategy\RandomPortStrategy;
use stdClass;
use Testcontainers\Containers\PortStrategy\AlreadyExistsPortStrategyException;
use Testcontainers\Containers\PortStrategy\PortStrategyProvider;
use UnexpectedValueException;

final class PortStrategyProviderTest extends TestCase
{
    /**
     * @return void
     */
    public function testRegister()
    {
        $provider = new PortStrategyProvider();
        $provider->register('test', new RandomPortStrategy());

        self::assertTrue(true);
    }

    /**
     * @return void
     */
    public function testRegisterAlreadyExistsPortStrategyException()
    {
        $this->expectExceptionMessage('Port strategy with name test already exists.');
        $this->expectException(AlreadyExistsPortStrategyException::class);

        $provider = new PortStrategyProvider();
        $provider->register('test', new RandomPortStrategy());
        $provider->register('test', new RandomPortStrategy());
    }

    /**
     * @return void
     */
    public function testGet()
    {
        $strategy = new RandomPortStrategy();
        $provider = new PortStrategyProvider();
        $provider->register('test', $strategy);

        self::assertSame($strategy, $provider->get('test'));
    }

    /**
     * @return void
     */
    public function testGetNotFound()
    {
        $provider = new PortStrategyProvider();

        self::assertNull($provider->get('test'));
    }

    /**
     * @return void
     */
    public function testRegisterRejectsUnrelatedObject()
    {
        $this->expectException(UnexpectedValueException::class);

        (new PortStrategyProvider())->register('bad', new stdClass());
    }
}
