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
}
