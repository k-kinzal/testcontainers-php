<?php

namespace Testcontainers\Docker\Exception;

/**
 * Exception thrown when a port is already allocated by another process.
 *
 * This exception is used to indicate that an attempt to allocate a port
 * has failed because the port is already in use. It extends the PortConflictException
 * class to provide additional context specific to port allocation conflicts.
 */
class PortAlreadyAllocatedException extends PortConflictException
{
    /**
     * Checks if the given output matches the "port is already allocated" error message.
     *
     * @param string $output the output to check
     *
     * @return bool true if the output matches the error message, false otherwise
     */
    public static function match($output)
    {
        return strpos($output, 'Error response from daemon:') !== false
            && strpos($output, 'port is already allocated') !== false;
    }
}
