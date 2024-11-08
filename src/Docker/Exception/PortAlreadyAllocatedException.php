<?php

namespace Testcontainers\Docker\Exception;

/**
 * Exception thrown when a port is already allocated by another process.
 *
 * This exception is used to indicate that an attempt to allocate a port
 * has failed because the port is already in use. It extends the DockerException
 * class to provide additional context specific to port allocation conflicts.
 */
class PortAlreadyAllocatedException extends DockerException
{
    /**
     * Checks if the given output matches the "port is already allocated" error message.
     *
     * @param string $output The output to check.
     * @return bool True if the output matches the error message, false otherwise.
     */
    public static function match($output)
    {
        return preg_match('/Error response from daemon: /', $output) !== false
            && preg_match('/port is already allocated\.$/', $output) !== false;
    }
}
