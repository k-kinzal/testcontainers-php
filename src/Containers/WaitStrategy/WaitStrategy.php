<?php

namespace Testcontainers\Containers\WaitStrategy;

use Testcontainers\Containers\ContainerInstance;

/**
 * WaitStrategy interface defines the contract for strategies that wait until a container instance is ready.
 * Implementations of this interface should provide the logic to determine when a container is fully operational.
 */
interface WaitStrategy
{
    /**
     * Waits until the container instance is ready.
     *
     * @param ContainerInstance $instance The container instance to check.
     * @return void
     */
    public function waitUntilReady($instance);
}
