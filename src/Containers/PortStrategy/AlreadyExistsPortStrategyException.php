<?php

namespace Testcontainers\Containers\PortStrategy;

use Exception;

use function Testcontainers\ensure;

/**
 * An exception thrown when a port strategy with the same name already exists.
 */
class AlreadyExistsPortStrategyException extends Exception
{
    /**
     * @param string         $name     the name of the port strategy that already exists
     * @param int            $code     the exception code
     * @param null|Exception $previous the previous exception
     */
    public function __construct($name, $code = 0, $previous = null)
    {
        ensure(is_string($name), '$name must be string');
        ensure(is_int($code), '$code must be int');
        ensure($previous === null || $previous instanceof Exception, '$previous must be null|Exception');

        parent::__construct('Port strategy with name '.$name.' already exists.', $code, $previous);
    }
}
