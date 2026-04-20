<?php

namespace Testcontainers\Containers\WaitStrategy;

use Exception;

use function Testcontainers\ensure;

/**
 * Exception thrown when a wait strategy with the same name already exists.
 *
 * This exception is used to indicate that an attempt was made to register
 * a wait strategy with a name that is already in use.
 */
class AlreadyExistsWaitStrategyException extends Exception
{
    /**
     * @param string $name the name of the wait strategy that already exists
     */
    public function __construct($name)
    {
        ensure(is_string($name), '$name must be string');

        parent::__construct("Wait strategy with name {$name} already exists.");
    }
}
