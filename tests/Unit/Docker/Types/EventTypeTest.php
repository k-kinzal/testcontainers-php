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
     * @return void
     */
    public function testFromString()
    {
        foreach (EventType::values() as $value) {
            $this->assertSame($value, EventType::fromString($value)->toString());
        }
    }

    /**
     * @return void
     */
    public function testFromStringWithInvalidValue()
    {
        $this->expectException(InvalidFormatException::class);

        EventType::fromString('invalid');
    }

}
