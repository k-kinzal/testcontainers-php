<?php

namespace Tests\Unit\Containers\PortStrategy;

use Testcontainers\Containers\PortStrategy\RandomPortStrategy;

/**
 * @internal
 * @coversNothing
 */
class RandomPortStrategyTest extends PortStrategyTestCase
{
    /**
     * {@inheritDoc}
     */
    public function resolvePortStrategy()
    {
        return new RandomPortStrategy();
    }
}
