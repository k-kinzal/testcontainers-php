<?php

namespace Testcontainers\Containers\StartupCheckStrategy;

/**
 * StartupCheckStrategy interface defines the contract for strategies that wait until a container startup is successful.
 * Implementations of this interface should provide the logic to determine when a container is fully operational.
 */
interface StartupCheckStrategy
{
    /**
     * Wait until the container startup is successful
     *
     * @param $containerId
     * @return bool
     */
    public function waitUntilStartupSuccessful($containerId);

    /**
     * Get the name of the strategy.
     *
     * @return string The name of the strategy.
     */
    public function getName();
}
