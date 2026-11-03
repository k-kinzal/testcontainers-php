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

    /**
     * @return void
     */
    public function testScopeFromString()
    {
        $this->assertTrue(EventScope::fromString('local')->isLocal());
        $this->assertTrue(EventScope::fromString('swarm')->isSwarm());
    }

    /**
     * @return void
     */
    public function testScopeFromStringWithInvalidValue()
    {
        $this->expectException(InvalidFormatException::class);

        EventScope::fromString('invalid');
    }
}
