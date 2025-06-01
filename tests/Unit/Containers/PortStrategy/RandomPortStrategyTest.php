<?php

namespace Tests\Unit\Containers\PortStrategy;

use Testcontainers\Containers\PortStrategy\RandomPortStrategy;

class RandomPortStrategyTest extends PortStrategyTestCase
{
    public function resolvePortStrategy()
    {
        return new RandomPortStrategy();
    }
}
