<?php

namespace Testcontainers\Containers\PortStrategy;

/**
 * An interface representing a port strategy.
 */
interface PortStrategy
{
    /**
     * Get the port number.
     *
     * @return int The port number.
     */
    public function getPort();

    /**
     * Get the conflict behavior of the port strategy.
     *
     * @return ConflictBehavior The conflict behavior of the port strategy.
     */
    public function conflictBehavior();
}
