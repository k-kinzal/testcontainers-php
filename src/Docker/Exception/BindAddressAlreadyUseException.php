<?php

namespace Testcontainers\Docker\Exception;

/**
 * Exception thrown when a bind address is already in use by another process.
 *
 * This exception is used to indicate that an attempt to bind an address
 * has failed because the address is already in use. It extends the DockerException
 * class to provide additional context specific to address binding conflicts.
 */
class BindAddressAlreadyUseException extends DockerException
{
    /**
     * Checks if the given output matches the "bind address already in use" error message.
     *
     * @param string $output the output to check
     *
     * @return bool true if the output matches the error message, false otherwise
     */
    public static function match($output)
    {
        return strpos($output, 'Error response from daemon:') !== false
            && strpos($output, 'address already in use') !== false;
    }
}
