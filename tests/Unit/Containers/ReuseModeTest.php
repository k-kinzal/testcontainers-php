<?php

/** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Unit\Containers;

use PHPUnit\Framework\TestCase;
use Testcontainers\Containers\ReuseMode;
use Testcontainers\Exceptions\InvalidFormatException;

/**
 * @internal
 *
 * @coversNothing
 */
class ReuseModeTest extends TestCase
{
    public function testReuseModeAdd()
    {
        $reuseMode = new ReuseMode(ReuseMode::$ADD);

        $this->assertSame(ReuseMode::$ADD, $reuseMode->toString());
    }

    public function testReuseModeRestart()
    {
        $reuseMode = new ReuseMode(ReuseMode::$RESTART);

        $this->assertSame(ReuseMode::$RESTART, $reuseMode->toString());
    }

    public function testReuseModeReuse()
    {
        $reuseMode = new ReuseMode(ReuseMode::$REUSE);

        $this->assertSame(ReuseMode::$REUSE, $reuseMode->toString());
    }

    public function testAdd()
    {
        $reuseMode = ReuseMode::ADD();

        $this->assertSame(ReuseMode::$ADD, $reuseMode->toString());
    }

    public function testRestart()
    {
        $reuseMode = ReuseMode::RESTART();

        $this->assertSame(ReuseMode::$RESTART, $reuseMode->toString());
    }

    public function testReuse()
    {
        $reuseMode = ReuseMode::REUSE();

        $this->assertSame(ReuseMode::$REUSE, $reuseMode->toString());
    }

    public function testFromStringWithAdd()
    {
        $reuseMode = ReuseMode::fromString(ReuseMode::$ADD);

        $this->assertSame(ReuseMode::$ADD, $reuseMode->toString());
    }

    public function testFromStringWithRestart()
    {
        $reuseMode = ReuseMode::fromString(ReuseMode::$RESTART);

        $this->assertSame(ReuseMode::$RESTART, $reuseMode->toString());
    }

    public function testFromStringWithReuse()
    {
        $reuseMode = ReuseMode::fromString(ReuseMode::$REUSE);

        $this->assertSame(ReuseMode::$REUSE, $reuseMode->toString());
    }

    public function testFromStringWithInvalidMode()
    {
        $this->expectException(InvalidFormatException::class);
        $this->expectExceptionMessage('Invalid format: `"invalid"`, expects: `add`, `restart`, `reuse`');

        ReuseMode::fromString('invalid');
    }

    public function testIsAdd()
    {
        $reuseMode = new ReuseMode(ReuseMode::$ADD);

        $this->assertTrue($reuseMode->isAdd());
        $this->assertFalse($reuseMode->isRestart());
        $this->assertFalse($reuseMode->isReuse());
    }

    public function testIsRestart()
    {
        $reuseMode = new ReuseMode(ReuseMode::$RESTART);

        $this->assertTrue($reuseMode->isRestart());
        $this->assertFalse($reuseMode->isAdd());
        $this->assertFalse($reuseMode->isReuse());
    }

    public function testIsReuse()
    {
        $reuseMode = new ReuseMode(ReuseMode::$REUSE);

        $this->assertTrue($reuseMode->isReuse());
        $this->assertFalse($reuseMode->isAdd());
        $this->assertFalse($reuseMode->isRestart());
    }

    public function testToString()
    {
        $reuseMode = ReuseMode::ADD();

        $this->assertSame('add', (string) $reuseMode);
    }
}
