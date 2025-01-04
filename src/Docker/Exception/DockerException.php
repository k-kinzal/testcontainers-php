<?php

namespace Testcontainers\Docker\Exception;

use RuntimeException;
use Symfony\Component\Process\Process;

/**
 * Exception thrown when a Docker command fails.
 *
 * This exception is specifically used to indicate that a Docker command
 * executed via the Symfony Process component has failed. It provides
 * detailed information about the command that was executed and the
 * exit code returned by the Docker process.
 */
class DockerException extends RuntimeException
{
    /**
     * @var Process
     */
    private $process;

    /**
     * @param Process $process
     */
    public function __construct($process)
    {
        $command = $process->getCommandLine();
        $exitCode = $process->getExitCode();
        parent::__construct(
            sprintf(
                "Failed to docker command: `%s`, exit code: `%s`, stderr: `%s`",
                $command,
                $exitCode,
                $process->getErrorOutput()
            )
        );

        $this->process = $process;
    }

    /**
     * Retrieves the error output from the Docker process.
     *
     * @return string The error output from the Docker process.
     */
    public function getErrorOutput()
    {
        return $this->process->getErrorOutput();
    }
}
