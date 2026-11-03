<?php

namespace Tests\Unit\Containers\PortStrategy;

use Testcontainers\Containers\PortStrategy\RandomPortStrategy;

/**
 * @internal
 *
 * @coversNothing
 */
final class RandomPortStrategyTest extends PortStrategyTestCase
{
    public function resolvePortStrategy()
    {
        return new RandomPortStrategy();
    }
}
