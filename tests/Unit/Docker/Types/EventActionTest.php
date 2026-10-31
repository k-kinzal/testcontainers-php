<?php

/** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Unit\Docker\Types;

use PHPUnit\Framework\TestCase;
use Testcontainers\Docker\Types\EventAction;
use Testcontainers\Exceptions\InvalidFormatException;

/**
 * @internal
 *
 * @coversNothing
 */
class EventActionTest extends TestCase
{
    public function testEventAction()
    {
        $action = new EventAction(EventAction::$START);

        $this->assertSame('start', $action->toString());
        $this->assertSame('start', $action->getName());
        $this->assertNull($action->getDetail());
    }

    public function testEventActionWithDetail()
    {
        $action = new EventAction(EventAction::$EXEC_START, "/bin/sh -c 'echo hello'");

        $this->assertSame("exec_start: /bin/sh -c 'echo hello'", $action->toString());
        $this->assertSame('exec_start', $action->getName());
        $this->assertSame("/bin/sh -c 'echo hello'", $action->getDetail());
    }

    public function testDie()
    {
        $action = EventAction::DIE_();

        $this->assertSame('die', $action->toString());
        $this->assertTrue($action->isDie());
    }

    public function testHealthStatusHealthy()
    {
        $action = EventAction::HEALTH_STATUS_HEALTHY();

        $this->assertSame('health_status: healthy', $action->toString());
        $this->assertTrue($action->isHealthStatus());
        $this->assertTrue($action->isHealthStatusHealthy());
        $this->assertFalse($action->isHealthStatusUnhealthy());
    }

    public function testFromString()
    {
        foreach (EventAction::values() as $value) {
            $action = EventAction::fromString($value);

            $this->assertSame($value, $action->toString());
            $this->assertNull($action->getDetail());
        }
    }

    public function testFromStringWithDetail()
    {
        $action = EventAction::fromString('exec_create: ls -la');

        $this->assertTrue($action->isExecCreate());
        $this->assertSame('ls -la', $action->getDetail());
        $this->assertSame('exec_create: ls -la', $action->toString());
    }

    public function testFromStringWithFreeFormHealthStatus()
    {
        $action = EventAction::fromString('health_status: curl: (7) Failed to connect');

        $this->assertTrue($action->isHealthStatus());
        $this->assertSame('curl: (7) Failed to connect', $action->getDetail());
    }

    public function testFromStringWithInvalidValue()
    {
        $this->expectException(InvalidFormatException::class);

        EventAction::fromString('invalid');
    }

    public function testFromStringWithDetailOnActionWithoutDetail()
    {
        $this->expectException(InvalidFormatException::class);

        EventAction::fromString('die: 1');
    }

    public function testIsMethodsAreExclusive()
    {
        $action = EventAction::CREATE();

        $this->assertTrue($action->isCreate());
        $this->assertFalse($action->isStart());
        $this->assertFalse($action->isDestroy());
    }
}
