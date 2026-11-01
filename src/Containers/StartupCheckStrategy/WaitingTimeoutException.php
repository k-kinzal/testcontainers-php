<?php

namespace Testcontainers\Containers\StartupCheckStrategy;

use Exception;
use RuntimeException;

/**
 * WaitingTimeoutException is thrown when a container fails to start within the specified timeout period.
 * @api
 */
class WaitingTimeoutException extends RuntimeException
{
    /**
     * Report that the container did not become ready before the timeout.
     *
     * @param int            $timeout  the timeout in seconds
     * @param null|string    $message  the exception message
     * @param int            $code     the exception code
     * @param null|Exception $previous the previous exception
     */
    public function __construct($timeout, $message = null, $code = 0, $previous = null)
    {
        parent::__construct(
            sprintf(
                'Waiting for container to be ready timed out after %d seconds: %s',
                $timeout,
                $message === null ? '' : $message
            ),
            $code,
            $previous
        );
    }
}
