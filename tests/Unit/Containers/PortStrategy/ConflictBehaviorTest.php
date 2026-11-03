<?php

/** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Unit\Containers\PortStrategy;

use PHPUnit\Framework\TestCase;
use Testcontainers\Containers\PortStrategy\ConflictBehavior;
use Testcontainers\Exceptions\InvalidFormatException;

final class ConflictBehaviorTest extends TestCase
{
    /**
     * @return void
     */
    public function testConstructRetry()
    {
        $conflictBehavior = new ConflictBehavior(ConflictBehavior::$RETRY);

        self::assertSame(ConflictBehavior::$RETRY, $conflictBehavior->toString());
    }

    /**
     * @return void
     */
    public function testConstructFail()
    {
        $conflictBehavior = new ConflictBehavior(ConflictBehavior::$FAIL);

        self::assertSame(ConflictBehavior::$FAIL, $conflictBehavior->toString());
    }

    /**
     * @return void
     */
    public function testRetry()
    {
        $conflictBehavior = ConflictBehavior::RETRY();

        self::assertSame(ConflictBehavior::$RETRY, $conflictBehavior->toString());
    }

    /**
     * @return void
     */
    public function testFail()
    {
        $conflictBehavior = ConflictBehavior::FAIL();

        self::assertSame(ConflictBehavior::$FAIL, $conflictBehavior->toString());
    }

    /**
     * @return void
     */
    public function testFromStringWithRetry()
    {
        $conflictBehavior = ConflictBehavior::fromString(ConflictBehavior::$RETRY);

        self::assertSame(ConflictBehavior::$RETRY, $conflictBehavior->toString());
    }

    /**
     * @return void
     */
    public function testFromStringWithFail()
    {
        $conflictBehavior = ConflictBehavior::fromString(ConflictBehavior::$FAIL);

        self::assertSame(ConflictBehavior::$FAIL, $conflictBehavior->toString());
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

        self::assertTrue($conflictBehavior->isRetry());
        self::assertFalse($conflictBehavior->isFail());
    }

    /**
     * @return void
     */
    public function testIsFail()
    {
        $conflictBehavior = new ConflictBehavior(ConflictBehavior::$FAIL);

        self::assertTrue($conflictBehavior->isFail());
        self::assertFalse($conflictBehavior->isRetry());
    }
}
