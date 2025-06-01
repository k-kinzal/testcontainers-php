<?php

namespace Tests\Unit\Containers\StartupCheckStrategy;

use Testcontainers\Containers\StartupCheckStrategy\IsRunningStartupCheckStrategy;

class IsRunningStartupCheckStrategyTest extends StartupCheckStrategyTestCase
{
    public function resolveStartupCheckStrategy()
    {
        return new IsRunningStartupCheckStrategy();
    }
}
