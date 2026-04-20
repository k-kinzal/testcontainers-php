<?php

namespace Testcontainers\Containers\StartupCheckStrategy;

use function Testcontainers\ensure;

/**
 * StartupCheckFailedException is thrown when a container's startup check strategy reports failure.
 */
class StartupCheckFailedException extends \RuntimeException
{
    /**
     * @param string          $message  the exception message
     * @param int             $code     the exception code
     * @param null|\Exception $previous the previous exception
     */
    public function __construct($message = 'illegal state of container', $code = 0, $previous = null)
    {
        ensure(is_string($message), '$message must be string');
        ensure(is_int($code), '$code must be int');
        ensure($previous === null || $previous instanceof \Exception, '$previous must be null|Exception');

        parent::__construct(
            sprintf('failed startup check: %s', $message),
            $code,
            $previous
        );
    }
}
