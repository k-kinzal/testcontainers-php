<?php

namespace Testcontainers\Containers\WaitStrategy;

use Exception;
use RuntimeException;

/**
 * Exception thrown when waiting for a container to be ready times out.
 *
 * This exception is used to indicate that the specified timeout duration
 * has been exceeded while waiting for the container to be ready.
 */
class WaitingTimeoutException extends RuntimeException
{
    /**
     * @param int $timeout The timeout in seconds.
     * @param int $code The exception code.
     * @param Exception|null $previous The previous exception.
     */
    public function __construct($timeout, $code = 0, $previous = null)
    {
        parent::__construct(
            sprintf(
                "Waiting for container to be ready timed out after %d seconds.",
                $timeout
            ),
            $code,
            $previous
        );
    }
}
