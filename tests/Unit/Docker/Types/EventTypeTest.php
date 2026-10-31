<?php

/** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Unit\Docker\Types;

use PHPUnit\Framework\TestCase;
use Testcontainers\Docker\Types\EventScope;
use Testcontainers\Docker\Types\EventType;
use Testcontainers\Exceptions\InvalidFormatException;

/**
 * @internal
 *
 * @coversNothing
 */
class EventTypeTest extends TestCase
{
    public function testContainer()
    {
        $type = EventType::CONTAINER();

        $this->assertSame('container', $type->toString());
        $this->assertTrue($type->isContainer());
        $this->assertFalse($type->isImage());
    }

    public function testFromString()
    {
        foreach (EventType::values() as $value) {
            $this->assertSame($value, EventType::fromString($value)->toString());
        }
    }

    public function testFromStringWithInvalidValue()
    {
        $this->expectException(InvalidFormatException::class);

        EventType::fromString('invalid');
    }

    public function testScopeFromString()
    {
        $this->assertTrue(EventScope::fromString('local')->isLocal());
        $this->assertTrue(EventScope::fromString('swarm')->isSwarm());
    }

    public function testScopeFromStringWithInvalidValue()
    {
        $this->expectException(InvalidFormatException::class);

        EventScope::fromString('invalid');
    }
}
