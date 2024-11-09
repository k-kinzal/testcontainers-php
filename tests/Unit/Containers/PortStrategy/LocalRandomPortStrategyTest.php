<?php

namespace Tests\Unit\Containers\PortStrategy;

use Testcontainers\Containers\PortStrategy\LocalRandomPortStrategy;

class LocalRandomPortStrategyTest extends PortStrategyTestCase
{
    /**
     * @inheritDoc
     */
    public function resolvePortStrategy()
    {
        return new LocalRandomPortStrategy();
    }
}
