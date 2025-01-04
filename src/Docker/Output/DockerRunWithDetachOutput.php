<?php

namespace Testcontainers\Docker\Output;

use Symfony\Component\Process\Process;
use Testcontainers\Docker\Types\ContainerId;

/**
 * Represents the output of a Docker `run` command executed via Symfony Process.
 *
 * This class extends DockerRunOutput to include the container ID of the Docker container
 * that was started by the `docker run` command.
 */
class DockerRunWithDetachOutput extends DockerRunOutput
{
    /**
     * The ID of the Docker container.
     *
     * This property holds the container ID of the Docker container that was started
     * by the `docker run` command executed by the Symfony Process instance.
     *
     * @var string
     */
    private $containerId;

    /**
     * @param Process $process The Symfony Process instance that executed the `docker run` command.
     */
    public function __construct($process)
    {
        parent::__construct($process);

        $this->containerId = new ContainerId(trim($process->getOutput()));
    }

    /**
     * Get the Docker container ID.
     *
     * This method returns the ID of the Docker container that was started
     * by the `docker run` command executed by the Symfony Process instance.
     *
     * @return string The Docker container ID.
     */
    public function getContainerId()
    {
        return $this->containerId;
    }
}
