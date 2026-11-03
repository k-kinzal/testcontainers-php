<?php

/** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Unit\Docker\Types;

use PHPUnit\Framework\TestCase;
use Testcontainers\Docker\Types\EventType;
use Testcontainers\Exceptions\InvalidFormatException;

final class EventTypeTest extends TestCase
{
    /**
     * @return void
     */
    public function testContainer()
    {
        $type = EventType::CONTAINER();

        $this->assertSame('container', $type->toString());
        $this->assertTrue($type->isContainer());
        $this->assertFalse($type->isImage());
    }

    /**
     * @dataProvider providerFromString
     * @param string $value
     * @return void
     */
    public function testFromString($value)
    {
        $action = EventType::fromString($value);

        $this->assertSame($value, $action->toString());
    }

    /**
     * @return void
     */
    public function testFromStringWithInvalidValue()
    {
        $this->expectException(InvalidFormatException::class);

        EventType::fromString('invalid');
    }

    /**
     * @return array<array<string>>
     */
    public static function providerFromString()
    {
        return array_chunk(EventType::values(), 1);
    }

}
