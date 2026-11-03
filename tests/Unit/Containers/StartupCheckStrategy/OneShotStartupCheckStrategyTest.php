<?php

namespace Tests\Unit\Containers\StartupCheckStrategy;

use Testcontainers\Containers\StartupCheckStrategy\OneShotStartupCheckStrategy;

final class OneShotStartupCheckStrategyTest extends StartupCheckStrategyTestCase
{
    /**
     * @return array<array{OneShotStartupCheckStrategy}>
     */
    public static function providerWaitUntilStartupSuccessful()
    {
        return [[new OneShotStartupCheckStrategy()]];
    }
}
