<?php

/** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Unit\Docker\Types;

use PHPUnit\Framework\TestCase;
use Testcontainers\Docker\Types\EventScope;
use Testcontainers\Exceptions\InvalidFormatException;

final class EventScopeTest extends TestCase
{
    /**
     * @return void
     */
    public function testFromString()
    {
        self::assertTrue(EventScope::fromString('local')->isLocal());
        self::assertTrue(EventScope::fromString('swarm')->isSwarm());
    }

    /**
     * @return void
     */
    public function testFromStringWithInvalidValue()
    {
        $this->expectException(InvalidFormatException::class);

        EventScope::fromString('invalid');
    }
}
