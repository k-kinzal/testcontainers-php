<?php

namespace Testcontainers\Containers\StartupCheckStrategy;

use Exception;

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
        parent::__construct("Startup strategy with name {$name} already exists.");
    }
}
