<?php

/** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Unit\Containers;

use PHPUnit\Framework\TestCase;
use Testcontainers\Containers\ReuseMode;
use Testcontainers\Exceptions\InvalidFormatException;

final class ReuseModeTest extends TestCase
{
    /**
     * @return void
     */
    public function testReuseModeAdd()
    {
        $reuseMode = new ReuseMode(ReuseMode::$ADD);

        self::assertSame(ReuseMode::$ADD, $reuseMode->toString());
    }

    /**
     * @return void
     */
    public function testReuseModeRestart()
    {
        $reuseMode = new ReuseMode(ReuseMode::$RESTART);

        self::assertSame(ReuseMode::$RESTART, $reuseMode->toString());
    }

    /**
     * @return void
     */
    public function testReuseModeReuse()
    {
        $reuseMode = new ReuseMode(ReuseMode::$REUSE);

        self::assertSame(ReuseMode::$REUSE, $reuseMode->toString());
    }

    /**
     * @return void
     */
    public function testAdd()
    {
        $reuseMode = ReuseMode::ADD();

        self::assertSame(ReuseMode::$ADD, $reuseMode->toString());
    }

    /**
     * @return void
     */
    public function testRestart()
    {
        $reuseMode = ReuseMode::RESTART();

        self::assertSame(ReuseMode::$RESTART, $reuseMode->toString());
    }

    /**
     * @return void
     */
    public function testReuse()
    {
        $reuseMode = ReuseMode::REUSE();

        self::assertSame(ReuseMode::$REUSE, $reuseMode->toString());
    }

    /**
     * @return void
     */
    public function testFromStringWithAdd()
    {
        $reuseMode = ReuseMode::fromString(ReuseMode::$ADD);

        self::assertSame(ReuseMode::$ADD, $reuseMode->toString());
    }

    /**
     * @return void
     */
    public function testFromStringWithRestart()
    {
        $reuseMode = ReuseMode::fromString(ReuseMode::$RESTART);

        self::assertSame(ReuseMode::$RESTART, $reuseMode->toString());
    }

    /**
     * @return void
     */
    public function testFromStringWithReuse()
    {
        $reuseMode = ReuseMode::fromString(ReuseMode::$REUSE);

        self::assertSame(ReuseMode::$REUSE, $reuseMode->toString());
    }

    /**
     * @return void
     */
    public function testFromStringWithInvalidMode()
    {
        $this->expectException(InvalidFormatException::class);
        $this->expectExceptionMessage('Invalid format: `"invalid"`, expects: `add`, `restart`, `reuse`');

        ReuseMode::fromString('invalid');
    }

    /**
     * @return void
     */
    public function testIsAdd()
    {
        $reuseMode = new ReuseMode(ReuseMode::$ADD);

        self::assertTrue($reuseMode->isAdd());
        self::assertFalse($reuseMode->isRestart());
        self::assertFalse($reuseMode->isReuse());
    }

    /**
     * @return void
     */
    public function testIsRestart()
    {
        $reuseMode = new ReuseMode(ReuseMode::$RESTART);

        self::assertTrue($reuseMode->isRestart());
        self::assertFalse($reuseMode->isAdd());
        self::assertFalse($reuseMode->isReuse());
    }

    /**
     * @return void
     */
    public function testIsReuse()
    {
        $reuseMode = new ReuseMode(ReuseMode::$REUSE);

        self::assertTrue($reuseMode->isReuse());
        self::assertFalse($reuseMode->isAdd());
        self::assertFalse($reuseMode->isRestart());
    }

    /**
     * @return void
     */
    public function testToString()
    {
        $reuseMode = ReuseMode::ADD();

        self::assertSame('add', (string) $reuseMode);
    }
}
