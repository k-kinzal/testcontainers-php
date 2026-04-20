<?php

namespace Testcontainers\Containers\StartupCheckStrategy;

use Exception;
use RuntimeException;

use function Testcontainers\ensure;

/**
 * WaitingTimeoutException is thrown when a container fails to start within the specified timeout period.
 */
class WaitingTimeoutException extends RuntimeException
{
    /**
     * @param int            $timeout  the timeout in seconds
     * @param null|string    $message  the exception message
     * @param int            $code     the exception code
     * @param null|Exception $previous the previous exception
     */
    public function __construct($timeout, $message = null, $code = 0, $previous = null)
    {
        /** @psalm-suppress RedundantConditionGivenDocblockType */
        ensure(is_int($timeout), '$timeout must be int');
        /** @psalm-suppress RedundantConditionGivenDocblockType */
        ensure($message === null || is_string($message), '$message must be null|string');
        /** @psalm-suppress RedundantConditionGivenDocblockType */
        ensure(is_int($code), '$code must be int');
        /** @psalm-suppress RedundantConditionGivenDocblockType */
        ensure($previous === null || $previous instanceof Exception, '$previous must be null|Exception');

        $messageText = $message !== null ? $message : '';
        parent::__construct(
            sprintf(
                'Waiting for container to be ready timed out after %d seconds: %s',
                $timeout,
                $messageText
            ),
            $code,
            $previous
        );
    }
}
