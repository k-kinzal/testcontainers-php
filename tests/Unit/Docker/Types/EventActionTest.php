<?php

/** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Unit\Docker\Types;

use PHPUnit\Framework\TestCase;
use Testcontainers\Docker\Types\EventAction;
use Testcontainers\Exceptions\InvalidFormatException;

final class EventActionTest extends TestCase
{
    /**
     * @return void
     */
    public function testConstruct()
    {
        $action = new EventAction(EventAction::$START);

        self::assertSame('start', $action->toString());
        self::assertSame('start', $action->getName());
        self::assertNull($action->getDetail());
    }

    /**
     * @return void
     */
    public function testConstructWithDetail()
    {
        $action = new EventAction(EventAction::$EXEC_START, "/bin/sh -c 'echo hello'");

        self::assertSame("exec_start: /bin/sh -c 'echo hello'", $action->toString());
        self::assertSame('exec_start', $action->getName());
        self::assertSame("/bin/sh -c 'echo hello'", $action->getDetail());
    }

    /**
     * @return void
     */
    public function testDie()
    {
        $action = EventAction::DIE_();

        self::assertSame('die', $action->toString());
        self::assertTrue($action->isDie());
    }

    /**
     * @return void
     */
    public function testHealthStatusHealthy()
    {
        $action = EventAction::HEALTH_STATUS_HEALTHY();

        self::assertSame('health_status: healthy', $action->toString());
        self::assertTrue($action->isHealthStatus());
        self::assertTrue($action->isHealthStatusHealthy());
        self::assertFalse($action->isHealthStatusUnhealthy());
    }

    /**
     * @dataProvider providerFromString
     * @param string $value
     * @return void
     */
    public function testFromString($value)
    {
        $action = EventAction::fromString($value);

        self::assertSame($value, $action->toString());
        self::assertNull($action->getDetail());
    }

    /**
     * @return void
     */
    public function testFromStringWithDetail()
    {
        $action = EventAction::fromString('exec_create: ls -la');

        self::assertTrue($action->isExecCreate());
        self::assertSame('ls -la', $action->getDetail());
        self::assertSame('exec_create: ls -la', $action->toString());
    }

    /**
     * @return void
     */
    public function testFromStringWithFreeFormHealthStatus()
    {
        $action = EventAction::fromString('health_status: curl: (7) Failed to connect');

        self::assertTrue($action->isHealthStatus());
        self::assertSame('curl: (7) Failed to connect', $action->getDetail());
    }

    /**
     * @return void
     */
    public function testFromStringWithInvalidValue()
    {
        $this->expectException(InvalidFormatException::class);

        EventAction::fromString('invalid');
    }

    /**
     * @return void
     */
    public function testFromStringWithDetailOnActionWithoutDetail()
    {
        $this->expectException(InvalidFormatException::class);

        EventAction::fromString('die: 1');
    }

    /**
     * @return void
     */
    public function testIsDieIsExclusiveOfOtherActions()
    {
        $action = EventAction::CREATE();

        self::assertTrue($action->isCreate());
        self::assertFalse($action->isStart());
        self::assertFalse($action->isDestroy());
    }
    /**
     * @return array<array<string>>
     */
    public static function providerFromString()
    {
        return array_chunk(EventAction::values(), 1);
    }

}
