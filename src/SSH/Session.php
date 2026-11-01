<?php

namespace Testcontainers\SSH;

use Symfony\Component\Process\Process;
use Testcontainers\Utility\Ensure;

/**
 * This class represents an SSH session managed by a Symfony Process.
 * It provides methods to check if the session is running and to stop the session.
 * @api
 */
class Session
{
    use Ensure;

    /**
     * The process to use for the SSH session.
     *
     * @var Process
     */
    private $process;

    /**
     * Create an SSH session for the supplied process.
     *
     * @param Process $process the process to use for the SSH session
     * @psalm-param mixed $process
     */
    public function __construct($process)
    {
        self::ensureInstanceOf($process, Process::class);

        $this->process = $process;
    }

    public function __destruct()
    {
        $this->stop();
    }

    /**
     * Check if the SSH session is currently running.
     *
     * @return bool
     */
    public function isRunning()
    {
        return $this->process->isRunning();
    }

    /**
     * Stops the SSH session.
     * @return void
     */
    public function stop()
    {
        $this->process->stop();
    }
}
