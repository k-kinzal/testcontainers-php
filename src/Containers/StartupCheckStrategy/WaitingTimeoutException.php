<?php

namespace Testcontainers\Containers\StartupCheckStrategy;

/**
 * WaitingTimeoutException is thrown when a container fails to start within the specified timeout period.
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
