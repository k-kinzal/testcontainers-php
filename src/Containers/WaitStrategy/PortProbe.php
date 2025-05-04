<?php

namespace Testcontainers\Containers\WaitStrategy;

/**
 * Interface PortProbe.
 *
 * Provides a method to check the availability of a specific port on a given host.
 */
interface PortProbe
{
    /**
     * Checks if the specified port on the given host is available.
     *
     * @param string $host the hostname or IP address to check
     * @param int    $port the port number to check
     *
     * @return bool true if the port is available, false otherwise
     */
    public function available($host, $port);
}
