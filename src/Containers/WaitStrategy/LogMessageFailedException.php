<?php

namespace Testcontainers\Containers\WaitStrategy;

/**
 * Exception thrown when a container log message matches the failure pattern.
 *
 * This exception is used to indicate that the container has produced a log message
 * that matches the specified failure pattern, signaling that the container has
 * entered an error state.
 */
class LogMessageFailedException extends \RuntimeException
{
    /**
     * @param string         $logLine  the log line that matched the failure pattern
     * @param int            $code     the exception code
     * @param null|\Exception $previous the previous exception
     */
    public function __construct($logLine, $code = 0, $previous = null)
    {
        parent::__construct(
            sprintf('Container log matched failure pattern: %s', $logLine),
            $code,
            $previous
        );
    }
}
