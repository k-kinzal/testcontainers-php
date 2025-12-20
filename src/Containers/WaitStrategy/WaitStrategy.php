<?php

namespace Testcontainers\Containers\WaitStrategy;

use Psr\Log\LoggerInterface;
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
     * @param ContainerInstance $instance the container instance to check
     */
    public function waitUntilReady($instance);

    /**
     * Set Logger instance.
     *
     * @param LoggerInterface $logger the logger instance
     *
     * @return self
     */
    public function withLogger($logger);
}
