<?php

namespace Testcontainers\Docker;

use Symfony\Component\Process\Process;

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
     * @param Process $process
     * @param string $containerId
     */
    public function __construct($process, $containerId)
    {
        parent::__construct($process);

        $this->containerId = $containerId;
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
