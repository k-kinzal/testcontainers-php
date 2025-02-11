<?php

namespace Tests\Unit\Containers\PortStrategy;

use Testcontainers\Containers\PortStrategy\RandomPortStrategy;

class LocalRandomPortStrategyTest extends PortStrategyTestCase
{
    /**
     * @inheritDoc
     */
    public function resolvePortStrategy()
    {
        return new RandomPortStrategy();
    }
}
