<?php

namespace Testcontainers\Containers\PortStrategy;

use Exception;

class AlreadyExistsPortStrategyException extends Exception
{
    public function __construct($name, $code = 0, $previous = null)
    {
        parent::__construct('Port strategy with name ' . $name . ' already exists.', $code, $previous);
    }
}
