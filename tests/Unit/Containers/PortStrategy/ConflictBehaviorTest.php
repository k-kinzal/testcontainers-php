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
final class ConflictBehaviorTest extends TestCase
{
    /**
     * @return void
     */
    public function testConflictBehaviorRetry()
    {
        $conflictBehavior = new ConflictBehavior(ConflictBehavior::$RETRY);

        $this->assertSame(ConflictBehavior::$RETRY, $conflictBehavior->toString());
    }

    /**
     * @return void
     */
    public function testConflictBehaviorFail()
    {
        $conflictBehavior = new ConflictBehavior(ConflictBehavior::$FAIL);

        $this->assertSame(ConflictBehavior::$FAIL, $conflictBehavior->toString());
    }

    /**
     * @return void
     */
    public function testRetry()
    {
        $conflictBehavior = ConflictBehavior::RETRY();

        $this->assertSame(ConflictBehavior::$RETRY, $conflictBehavior->toString());
    }

    /**
     * @return void
     */
    public function testFail()
    {
        $conflictBehavior = ConflictBehavior::FAIL();

        $this->assertSame(ConflictBehavior::$FAIL, $conflictBehavior->toString());
    }

    /**
     * @return void
     */
    public function testFromStringWithRetry()
    {
        $conflictBehavior = ConflictBehavior::fromString(ConflictBehavior::$RETRY);

        $this->assertSame(ConflictBehavior::$RETRY, $conflictBehavior->toString());
    }

    /**
     * @return void
     */
    public function testFromStringWithFail()
    {
        $conflictBehavior = ConflictBehavior::fromString(ConflictBehavior::$FAIL);

        $this->assertSame(ConflictBehavior::$FAIL, $conflictBehavior->toString());
    }

    /**
     * @return void
     */
    public function testFromStringWithInvalidAction()
    {
        $this->expectException(InvalidFormatException::class);
        $this->expectExceptionMessage('Invalid format: `"invalid"`, expects: `retry`, `fail`');

        ConflictBehavior::fromString('invalid');
    }

    /**
     * @return void
     */
    public function testIsRetry()
    {
        $conflictBehavior = new ConflictBehavior(ConflictBehavior::$RETRY);

        $this->assertTrue($conflictBehavior->isRetry());
        $this->assertFalse($conflictBehavior->isFail());
    }

    /**
     * @return void
     */
    public function testIsFail()
    {
        $conflictBehavior = new ConflictBehavior(ConflictBehavior::$FAIL);

        $this->assertTrue($conflictBehavior->isFail());
        $this->assertFalse($conflictBehavior->isRetry());
    }
}
