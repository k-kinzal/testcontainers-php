<?php

namespace Testcontainers\Docker\Exception;

use function Testcontainers\ensure;

/**
 * Exception thrown when a bind address is already in use by another process.
 *
 * This exception is used to indicate that an attempt to bind an address
 * has failed because the address is already in use. It extends the PortConflictException
 * class to provide additional context specific to address binding and port conflicts.
 */
class BindAddressAlreadyUseException extends PortConflictException
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
        /** @psalm-suppress RedundantConditionGivenDocblockType */
        ensure(is_string($output), '$output must be string');

        return strpos($output, 'Error response from daemon:') !== false
            && strpos($output, 'address already in use') !== false;
    }
}
