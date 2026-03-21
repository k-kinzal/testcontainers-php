<?php

namespace Testcontainers\Containers\StartupCheckStrategy;

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
        parent::__construct(
            sprintf('failed startup check: %s', $message),
            $code,
            $previous
        );
    }
}
