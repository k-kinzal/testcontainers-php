<?php

namespace Symfony\Component\Process\Exception;

use RuntimeException as BaseRuntimeException;
use Symfony\Component\Process\Process;

/**
 * Base class for Symfony Process 3.4 runtime failures.
 */
class RuntimeException extends BaseRuntimeException
{
}

/**
 * Symfony Process 3.4 timeout exception.
 */
class ProcessTimedOutException extends RuntimeException
{
    /**
     * Return the process that exceeded its timeout.
     *
     * @return Process
     */
    public function getProcess()
    {
    }
}
