<?php

namespace Tests\Unit\Containers\PortStrategy;

use PHPUnit\Framework\TestCase;
use Testcontainers\Containers\PortStrategy\ConflictBehavior;
use Testcontainers\Containers\PortStrategy\PortStrategy;

abstract class PortStrategyTestCase extends TestCase
{
    /**
     * @return array<array{PortStrategy}>
     */
    abstract public static function providerGetPort();

    /**
     * @return array<array{PortStrategy}>
     */
    abstract public static function providerConflictBehavior();

    /**
     * @dataProvider providerGetPort
     * @param PortStrategy $strategy
     * @return void
     */
    public function testGetPort($strategy)
    {
        $port = $strategy->getPort();

        self::assertGreaterThanOrEqual(49152, $port);
        self::assertLessThanOrEqual(65535, $port);
    }

    /**
     * @dataProvider providerConflictBehavior
     * @param PortStrategy $strategy
     * @return void
     */
    public function testConflictBehavior($strategy)
    {
        $conflictBehavior = $strategy->conflictBehavior();

        self::assertInstanceOf(ConflictBehavior::class, $conflictBehavior);
    }
}
