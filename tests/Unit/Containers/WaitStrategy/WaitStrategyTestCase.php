<?php

namespace Tests\Unit\Containers\WaitStrategy;

use PHPUnit\Framework\TestCase;
use Testcontainers\Containers\WaitStrategy\WaitStrategy;

abstract class WaitStrategyTestCase extends TestCase
{
    /**
     * @return WaitStrategy
     */
    abstract public function resolveWaitStrategy();

    public function testGetName()
    {
        $strategy = $this->resolveWaitStrategy();
        $name = $strategy->getName();

        $this->assertTrue(is_string($name));
        $this->assertNotEmpty($name);
        $this->assertTrue(preg_match('/^[a-z_][a-z0-9_]*$/', $name) === 1);
    }
}
