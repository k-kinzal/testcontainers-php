<?php

namespace Tests\Unit\Containers\PortStrategy;

use Testcontainers\Containers\PortStrategy\StaticPortStrategy;

/**
 * @internal
 *
 * @coversNothing
 */
class StaticPortStrategyTest extends PortStrategyTestCase
{
    public function resolvePortStrategy()
    {
        return new StaticPortStrategy(50000);
    }

    public function testInterfaceGetPort()
    {
        $strategy = $this->resolvePortStrategy();
        $port = $strategy->getPort();

        $this->assertSame(50000, $port);
    }

    public function testReturnsSpecifiedPort()
    {
        $strategy1 = new StaticPortStrategy(50001);
        $strategy2 = new StaticPortStrategy(50002);

        $this->assertSame(50001, $strategy1->getPort());
        $this->assertSame(50002, $strategy2->getPort());
    }

    public function testConflictBehaviorIsFail()
    {
        $strategy = $this->resolvePortStrategy();
        $behavior = $strategy->conflictBehavior();

        $this->assertTrue($behavior->isFail());
        $this->assertFalse($behavior->isRetry());
    }
}
