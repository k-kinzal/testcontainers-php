<?php

namespace Tests\Unit\Containers\StartupCheckStrategy;

use Testcontainers\Containers\StartupCheckStrategy\IsRunningStartupCheckStrategy;

final class IsRunningStartupCheckStrategyTest extends StartupCheckStrategyTestCase
{
    /**
     * @return array<array{IsRunningStartupCheckStrategy}>
     */
    public static function providerWaitUntilStartupSuccessful()
    {
        return [[new IsRunningStartupCheckStrategy()]];
    }
}
