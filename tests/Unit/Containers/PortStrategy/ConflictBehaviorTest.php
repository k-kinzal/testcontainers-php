<?php

/** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Unit\Containers\PortStrategy;

use PHPUnit\Framework\TestCase;
use Testcontainers\Containers\PortStrategy\ConflictBehavior;
use Testcontainers\Exceptions\InvalidFormatException;

/**
 * @internal
 *
 * @coversNothing
 */
class ConflictBehaviorTest extends TestCase
{
    public function testConflictBehaviorRetry()
    {
        $conflictBehavior = new ConflictBehavior(ConflictBehavior::$RETRY);

        $this->assertSame(ConflictBehavior::$RETRY, $conflictBehavior->toString());
    }

    public function testConflictBehaviorFail()
    {
        $conflictBehavior = new ConflictBehavior(ConflictBehavior::$FAIL);

        $this->assertSame(ConflictBehavior::$FAIL, $conflictBehavior->toString());
    }

    public function testRetry()
    {
        $conflictBehavior = ConflictBehavior::RETRY();

        $this->assertSame(ConflictBehavior::$RETRY, $conflictBehavior->toString());
    }

    public function testFail()
    {
        $conflictBehavior = ConflictBehavior::FAIL();

        $this->assertSame(ConflictBehavior::$FAIL, $conflictBehavior->toString());
    }

    public function testFromStringWithRetry()
    {
        $conflictBehavior = ConflictBehavior::fromString(ConflictBehavior::$RETRY);

        $this->assertSame(ConflictBehavior::$RETRY, $conflictBehavior->toString());
    }

    public function testFromStringWithFail()
    {
        $conflictBehavior = ConflictBehavior::fromString(ConflictBehavior::$FAIL);

        $this->assertSame(ConflictBehavior::$FAIL, $conflictBehavior->toString());
    }

    public function testFromStringWithInvalidAction()
    {
        $this->expectException(InvalidFormatException::class);
        $this->expectExceptionMessage('Invalid format: `"invalid"`, expects: `retry`, `fail`');

        ConflictBehavior::fromString('invalid');
    }

    public function testIsRetry()
    {
        $conflictBehavior = new ConflictBehavior(ConflictBehavior::$RETRY);

        $this->assertTrue($conflictBehavior->isRetry());
        $this->assertFalse($conflictBehavior->isFail());
    }

    public function testIsFail()
    {
        $conflictBehavior = new ConflictBehavior(ConflictBehavior::$FAIL);

        $this->assertTrue($conflictBehavior->isFail());
        $this->assertFalse($conflictBehavior->isRetry());
    }
}
