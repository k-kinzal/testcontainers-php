<?php

namespace Tests\Unit\Containers\StartupCheckStrategy;

use Testcontainers\Containers\StartupCheckStrategy\OneShotStartupCheckStrategy;

/**
 * @internal
 *
 * @coversNothing
 */
final class OneShotStartupCheckStrategyTest extends StartupCheckStrategyTestCase
{
    public function resolveStartupCheckStrategy()
    {
        return new OneShotStartupCheckStrategy();
    }
}
