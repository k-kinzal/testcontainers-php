<?php

namespace Tests\Unit\Containers\PortStrategy;

use Testcontainers\Containers\PortStrategy\RandomPortStrategy;

final class RandomPortStrategyTest extends PortStrategyTestCase
{
    /**
     * @return array<array{RandomPortStrategy}>
     */
    public static function providerGetPort()
    {
        return [[new RandomPortStrategy()]];
    }

    /**
     * @return array<array{RandomPortStrategy}>
     */
    public static function providerConflictBehavior()
    {
        return [[new RandomPortStrategy()]];
    }
}
