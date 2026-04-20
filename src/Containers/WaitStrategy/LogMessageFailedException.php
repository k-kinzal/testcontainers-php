<?php

namespace Testcontainers\Containers\WaitStrategy;

use Exception;
use RuntimeException;

use function Testcontainers\ensure;

/**
 * Exception thrown when a container log message matches the failure pattern.
 *
 * This exception is used to indicate that the container has produced a log message
 * that matches the specified failure pattern, signaling that the container has
 * entered an error state.
 */
class LogMessageFailedException extends RuntimeException
{
    /**
     * @param string         $logLine  the log line that matched the failure pattern
     * @param int            $code     the exception code
     * @param null|Exception $previous the previous exception
     */
    public function __construct($logLine, $code = 0, $previous = null)
    {
        ensure(is_string($logLine), '$logLine must be string');
        ensure(is_int($code), '$code must be int');
        ensure($previous === null || $previous instanceof Exception, '$previous must be null|Exception');

        parent::__construct(
            sprintf('Container log matched failure pattern: %s', $logLine),
            $code,
            $previous
        );
    }
}
