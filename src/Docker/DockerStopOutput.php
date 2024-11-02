<?php

namespace Testcontainers\Docker;

use Symfony\Component\Process\Process;

/**
 * Handles the output of a Docker `stop` command executed via Symfony Process.
 *
 * This class extends DockerOutput to provide methods for retrieving the IDs
 * of Docker containers that were stopped by the `docker stop` command.
 */
class DockerStopOutput extends DockerOutput
{
    /**
     * An array of Docker container IDs.
     *
     * This property holds the IDs of the Docker containers that were stopped
     * by the `docker stop` command executed by the Symfony Process instance.
     *
     * @var string[]
     */
    private $containerIds;

    /**
     * @param Process $process
     * @param string[] $containerIds
     */
    public function __construct($process, $containerIds)
    {
        parent::__construct($process);

        $this->containerIds = $containerIds;
    }

    /**
     * Get the IDs of the Docker containers that were stopped.
     *
     * This method returns an array of container IDs that were stopped
     * by the `docker stop` command executed by the Symfony Process instance.
     *
     * @return string[] An array of Docker container IDs.
     */
    public function getContainerIds()
    {
        return $this->containerIds;
    }
}
