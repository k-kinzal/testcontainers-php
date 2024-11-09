<?php

namespace Testcontainers\Containers\WaitStrategy;

use Exception;

/**
 * Exception thrown when a wait strategy with the same name already exists.
 *
 * This exception is used to indicate that an attempt was made to register
 * a wait strategy with a name that is already in use.
 */
class AlreadyExistsWaitStrategyException extends Exception
{
    /**
     * @param string $name The name of the wait strategy that already exists.
     */
    public function __construct($name)
    {
        parent::__construct("Wait strategy with name $name already exists.");
    }
}
