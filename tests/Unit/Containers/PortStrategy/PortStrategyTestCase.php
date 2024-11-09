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

        $this->assertTrue(is_int($port));
        $this->assertGreaterThanOrEqual(49152, $port);
        $this->assertLessThanOrEqual(65535, $port);
    }

    public function testInterfaceGetName()
    {
        $strategy = $this->resolvePortStrategy();
        $name = $strategy->getName();

        $this->assertTrue(is_string($name));
        $this->assertNotEmpty($name);
        $this->assertTrue(preg_match('/^[a-z_][a-z0-9_]*$/', $name) === 1);
    }

    public function testInterfaceGetNameConsistency()
    {
        $strategy = $this->resolvePortStrategy();
        $name1 = $strategy->getName();
        $name2 = $strategy->getName();

        $this->assertSame($name1, $name2);
    }

    public function testInterfaceConflictBehavior()
    {
        $strategy = $this->resolvePortStrategy();
        $conflictBehavior = $strategy->conflictBehavior();

        $this->assertInstanceOf(ConflictBehavior::class, $conflictBehavior);
    }
}
