<?php

namespace Testcontainers\Containers\WaitStrategy;

/**
 * Interface PortProbe
 *
 * Provides a method to check the availability of a specific port on a given host.
 */
interface PortProbe
{
    /**
     * Checks if the specified port on the given host is available.
     *
     * @param string $host The hostname or IP address to check.
     * @param int $port The port number to check.
     * @return bool True if the port is available, false otherwise.
     */
    public function available($host, $port);
}
