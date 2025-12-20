<?php

namespace Testcontainers\Containers\StartupCheckStrategy;

use Psr\Log\LoggerInterface;
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

    /**
     * Set Logger instance.
     *
     * @param LoggerInterface $logger the logger instance
     *
     * @return self
     */
    public function withLogger($logger);
}
