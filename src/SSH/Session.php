<?php

namespace Testcontainers\SSH;

use Symfony\Component\Process\Process;

/**
 * This class represents an SSH session managed by a Symfony Process.
 * It provides methods to check if the session is running and to stop the session.
 */
class Session
{
    /**
     * The process to use for the SSH session.
     *
     * @var Process
     */
    private $process;

    /**
     * @param Process $process The process to use for the SSH session.
     */
    public function __construct($process)
    {
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
     *
     * @return void
     */
    public function stop()
    {
        $this->process->stop();
    }
}
