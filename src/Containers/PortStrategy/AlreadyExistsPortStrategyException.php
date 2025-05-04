<?php

namespace Testcontainers\Containers\PortStrategy;

use Exception;

/**
 * An exception thrown when a port strategy with the same name already exists.
 */
class AlreadyExistsPortStrategyException extends Exception
{
    /**
     * @param string $name The name of the port strategy that already exists.
     * @param int $code The exception code.
     * @param Exception|null $previous The previous exception.
     */
    public function __construct($name, $code = 0, $previous = null)
    {
        parent::__construct('Port strategy with name ' . $name . ' already exists.', $code, $previous);
    }
}
