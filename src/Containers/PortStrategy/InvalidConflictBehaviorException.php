<?php

namespace Testcontainers\Containers\PortStrategy;

use Exception;

/**
 * Exception thrown when an invalid conflict behavior is used.
 *
 * This exception is used to indicate that an operation attempted to use
 * a conflict behavior that is not recognized or supported. It extends
 * the base Exception class to provide additional context specific to
 * invalid conflict behaviors.
 */
class InvalidConflictBehaviorException extends Exception
{
    /**
     * @param string $action The action that caused the conflict.
     * @param int $code The exception code (default is 0).
     * @param Exception|null $previous The previous exception used for exception chaining.
     */
    public function __construct($action, $code = 0, $previous = null)
    {
        parent::__construct("Invalid conflict behavior: $action", $code, $previous);
    }
}
