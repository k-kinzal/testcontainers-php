<?php

/** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Unit\Containers\Types;

use PHPUnit\Framework\TestCase;
use Testcontainers\Containers\Types\BindMode;
use Testcontainers\Exceptions\InvalidFormatException;

final class BindModeTest extends TestCase
{
    /**
     * @return void
     */
    public function testConstructReadOnly()
    {
        $bindMode = new BindMode(BindMode::$READ_ONLY);

        $this->assertSame(BindMode::$READ_ONLY, $bindMode->toString());
    }

    /**
     * @return void
     */
    public function testConstructReadWrite()
    {
        $bindMode = new BindMode(BindMode::$READ_WRITE);

        $this->assertSame(BindMode::$READ_WRITE, $bindMode->toString());
    }

    /**
     * @return void
     */
    public function testReadOnly()
    {
        $bindMode = BindMode::READ_ONLY();

        $this->assertSame(BindMode::$READ_ONLY, $bindMode->toString());
    }

    /**
     * @return void
     */
    public function testReadWrite()
    {
        $bindMode = BindMode::READ_WRITE();

        $this->assertSame(BindMode::$READ_WRITE, $bindMode->toString());
    }

    /**
     * @return void
     */
    public function testFromStringWithReadOnly()
    {
        $bindMode = BindMode::fromString(BindMode::$READ_ONLY);

        $this->assertSame(BindMode::$READ_ONLY, $bindMode->toString());
    }

    /**
     * @return void
     */
    public function testFromStringWithReadWrite()
    {
        $bindMode = BindMode::fromString(BindMode::$READ_WRITE);

        $this->assertSame(BindMode::$READ_WRITE, $bindMode->toString());
    }

    /**
     * @return void
     */
    public function testFromStringWithInvalidMode()
    {
        $this->expectException(InvalidFormatException::class);
        $this->expectExceptionMessage('Invalid format: `"invalid"`, expects: `ro`, `rw`');

        BindMode::fromString('invalid');
    }

    /**
     * @return void
     */
    public function testIsReadOnly()
    {
        $bindMode = new BindMode(BindMode::$READ_ONLY);

        $this->assertTrue($bindMode->isReadOnly());
    }

    /**
     * @return void
     */
    public function testIsReadWrite()
    {
        $bindMode = new BindMode(BindMode::$READ_WRITE);

        $this->assertTrue($bindMode->isReadWrite());
    }

    /**
     * @return void
     */
    public function testConstructRejectsUnknownMode()
    {
        $this->expectException(InvalidFormatException::class);

        new BindMode('invalid');
    }
}
