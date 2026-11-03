<?php

namespace Tests\Unit\Containers\PortStrategy;

use Testcontainers\Containers\PortStrategy\StaticPortStrategy;
use UnexpectedValueException;

final class StaticPortStrategyTest extends PortStrategyTestCase
{
    /**
     * @return array<array{StaticPortStrategy}>
     */
    public static function providerGetPort()
    {
        return [[new StaticPortStrategy(50000)]];
    }

    /**
     * @return array<array{StaticPortStrategy}>
     */
    public static function providerConflictBehavior()
    {
        return [[new StaticPortStrategy(50000)]];
    }

    /**
     * @return void
     */
    public function testGetPortUsesConfiguredValue()
    {
        $strategy = new StaticPortStrategy(50000);
        $port = $strategy->getPort();

        self::assertSame(50000, $port);
    }

    /**
     * @return void
     */
    public function testGetPortReturnsSpecifiedPort()
    {
        $strategy1 = new StaticPortStrategy(50001);
        $strategy2 = new StaticPortStrategy(50002);

        self::assertSame(50001, $strategy1->getPort());
        self::assertSame(50002, $strategy2->getPort());
    }

    /**
     * @return void
     */
    public function testConflictBehaviorIsFail()
    {
        $strategy = new StaticPortStrategy(50000);
        $behavior = $strategy->conflictBehavior();

        self::assertTrue($behavior->isFail());
        self::assertFalse($behavior->isRetry());
    }

    /**
     * @return void
     */
    public function testConstructRejectsBooleanPort()
    {
        $this->expectException(UnexpectedValueException::class);

        new StaticPortStrategy(false);
    }
}
