<?php

namespace Tests\Unit\Containers\PortStrategy;

use Testcontainers\Containers\PortStrategy\RandomPortStrategy;

final class RandomPortStrategyTest extends PortStrategyTestCase
{
    public function resolvePortStrategy()
    {
        return new RandomPortStrategy();
    }
}
