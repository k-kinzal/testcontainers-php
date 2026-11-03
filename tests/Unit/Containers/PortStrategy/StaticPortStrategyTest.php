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

        $this->assertSame(50000, $port);
    }

    /**
     * @return void
     */
    public function testGetPortReturnsSpecifiedPort()
    {
        $strategy1 = new StaticPortStrategy(50001);
        $strategy2 = new StaticPortStrategy(50002);

        $this->assertSame(50001, $strategy1->getPort());
        $this->assertSame(50002, $strategy2->getPort());
    }

    /**
     * @return void
     */
    public function testConflictBehaviorIsFail()
    {
        $strategy = new StaticPortStrategy(50000);
        $behavior = $strategy->conflictBehavior();

        $this->assertTrue($behavior->isFail());
        $this->assertFalse($behavior->isRetry());
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
