<?php

namespace Testcontainers\Docker\Exception;

use Symfony\Component\Process\Process;

use function Testcontainers\ensure;

/**
 * Exception thrown when a Docker command fails.
 *
 * This exception is specifically used to indicate that a Docker command
 * executed via the Symfony Process component has failed. It provides
 * detailed information about the command that was executed and the
 * exit code returned by the Docker process.
 */
class DockerException extends \RuntimeException
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
        ensure($process instanceof Process, '$process must be Process');

        $command = $process->getCommandLine();
        $exitCode = $process->getExitCode();
        $exitCodeText = $exitCode !== null ? $exitCode : 'unknown';
        parent::__construct(
            sprintf(
                'Failed to docker command: `%s`, exit code: `%s`, stderr: `%s`',
                $command,
                $exitCodeText,
                $process->getErrorOutput()
            )
        );

        $this->process = $process;
    }

    /**
     * Retrieves the error output from the Docker process.
     *
     * @return string the error output from the Docker process
     */
    public function getErrorOutput()
    {
        return $this->process->getErrorOutput();
    }
}
