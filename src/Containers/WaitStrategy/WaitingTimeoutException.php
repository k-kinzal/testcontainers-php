<?php

namespace Testcontainers\Containers\WaitStrategy;

use Exception;

/**
 * Exception thrown when waiting for a container to be ready times out.
 *
 * This exception is used to indicate that the specified timeout duration
 * has been exceeded while waiting for the container to be ready.
 */
class WaitingTimeoutException extends \RuntimeException
{
    /**
     * @param int             $timeout  the timeout in seconds
     * @param null|string     $message  the exception message
     * @param int             $code     the exception code
     * @param null|\Exception $previous the previous exception
     */
    public function __construct($timeout, $message = null, $code = 0, $previous = null)
    {
        parent::__construct(
            sprintf(
                'Waiting for container to be ready timed out after %d seconds: %s',
                $timeout,
                $message
            ),
            $code,
            $previous
        );
    }
}
