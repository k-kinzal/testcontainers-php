<?php

namespace Testcontainers\Containers\StartupCheckStrategy;

use Exception;

use function Testcontainers\ensure;

/**
 * An exception thrown when a startup strategy with the same name already exists.
 */
class AlreadyExistsStartupStrategyException extends Exception
{
    /**
     * @param string $name the name of the startup strategy that already exists
     */
    public function __construct($name)
    {
        ensure(is_string($name), '$name must be string');

        parent::__construct("Startup strategy with name {$name} already exists.");
    }
}
