<?php

namespace Testcontainers\Containers\StartupCheckStrategy;

use Testcontainers\Containers\ContainerInstance;

/**
 * StartupCheckStrategy interface defines the contract for strategies that wait until a container startup is successful.
 * Implementations of this interface should provide the logic to determine when a container is fully operational.
 */
interface StartupCheckStrategy
{
    /**
     * Wait until the container startup is successful.
     *
     * @param ContainerInstance $instance the container instance to check
     *
     * @return bool
     */
    public function waitUntilStartupSuccessful($instance);
}
