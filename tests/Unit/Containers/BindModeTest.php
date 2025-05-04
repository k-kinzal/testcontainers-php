<?php

/** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Unit\Containers;

use PHPUnit\Framework\TestCase;
use Testcontainers\Containers\Types\BindMode;
use Testcontainers\Exceptions\InvalidFormatException;

class BindModeTest extends TestCase
{
    public function testBindModeReadOnly()
    {
        $bindMode = new BindMode(BindMode::$READ_ONLY);

        $this->assertSame(BindMode::$READ_ONLY, $bindMode->toString());
    }

    public function testBindModeReadWrite()
    {
        $bindMode = new BindMode(BindMode::$READ_WRITE);

        $this->assertSame(BindMode::$READ_WRITE, $bindMode->toString());
    }

    public function testReadOnly()
    {
        $bindMode = BindMode::READ_ONLY();

        $this->assertSame(BindMode::$READ_ONLY, $bindMode->toString());
    }

    public function testReadWrite()
    {
        $bindMode = BindMode::READ_WRITE();

        $this->assertSame(BindMode::$READ_WRITE, $bindMode->toString());
    }

    public function testFromStringWithReadOnly()
    {
        $bindMode = BindMode::fromString(BindMode::$READ_ONLY);

        $this->assertSame(BindMode::$READ_ONLY, $bindMode->toString());
    }

    public function testFromStringWithReadWrite()
    {
        $bindMode = BindMode::fromString(BindMode::$READ_WRITE);

        $this->assertSame(BindMode::$READ_WRITE, $bindMode->toString());
    }

    public function testFromStringWithInvalidMode()
    {
        $this->expectException(InvalidFormatException::class);
        $this->expectExceptionMessage('Invalid format: `"invalid"`, expects: `ro`, `rw`');

        BindMode::fromString('invalid');
    }

    public function testIsReadOnly()
    {
        $bindMode = new BindMode(BindMode::$READ_ONLY);

        $this->assertTrue($bindMode->isReadOnly());
    }

    public function testIsReadWrite()
    {
        $bindMode = new BindMode(BindMode::$READ_WRITE);

        $this->assertTrue($bindMode->isReadWrite());
    }
}
