<?php

namespace Testcontainers\Containers\StartupCheckStrategy;

use Exception;
use RuntimeException;

/**
 * StartupCheckFailedException is thrown when a container's startup check strategy reports failure.
 * @api
 */
class StartupCheckFailedException extends RuntimeException
{
    /**
     * Report a failed container startup check.
     *
     * @param string         $message  the exception message
     * @param int            $code     the exception code
     * @param null|Exception $previous the previous exception
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
