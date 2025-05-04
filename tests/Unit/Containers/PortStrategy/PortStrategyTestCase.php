<?php

namespace Tests\Unit\Containers\PortStrategy;

use PHPUnit\Framework\TestCase;
use Testcontainers\Containers\PortStrategy\ConflictBehavior;
use Testcontainers\Containers\PortStrategy\PortStrategy;

abstract class PortStrategyTestCase extends TestCase
{
    /**
     * @return PortStrategy
     */
    abstract public function resolvePortStrategy();

    public function testInterfaceGetPort()
    {
        $strategy = $this->resolvePortStrategy();
        $port = $strategy->getPort();

        $this->assertGreaterThanOrEqual(49152, $port);
        $this->assertLessThanOrEqual(65535, $port);
    }

    public function testInterfaceConflictBehavior()
    {
        $strategy = $this->resolvePortStrategy();
        $conflictBehavior = $strategy->conflictBehavior();

        $this->assertInstanceOf(ConflictBehavior::class, $conflictBehavior);
    }
}
