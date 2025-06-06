<?php

namespace Testcontainers\Containers\PortStrategy;

use Exception;

/**
 * An exception thrown when a port strategy with the same name already exists.
 */
class AlreadyExistsPortStrategyException extends \Exception
{
    /**
     * @param string          $name     the name of the port strategy that already exists
     * @param int             $code     the exception code
     * @param null|\Exception $previous the previous exception
     */
    public function __construct($name, $code = 0, $previous = null)
    {
        parent::__construct('Port strategy with name '.$name.' already exists.', $code, $previous);
    }
}
