<?php

namespace Testcontainers\Docker\Output;

use Symfony\Component\Process\Process;
use Testcontainers\Docker\Types\ContainerId;

/**
 * Represents the output of a Docker `stop` command executed via Symfony Process.
 *
 * This class extends DockerOutput to include the container IDs of the Docker containers that were stopped
 */
class DockerStopOutput extends DockerOutput
{
    /**
     * An array of Docker container IDs.
     *
     * This property holds the IDs of the Docker containers that were stopped
     * by the `docker stop` command executed by the Symfony Process instance.
     *
     * @var ContainerId[]
     */
    private $containerIds;

    /**
     * @param Process $process
     */
    public function __construct($process)
    {
        parent::__construct($process);

        $output = $process->getOutput();
        $containerIds = [];
        foreach (explode("\n", $output) as $line) {
            $line = trim($line);
            if (empty($line)) {
                continue;
            }
            $containerIds[] = new ContainerId($line);
        }

        $this->containerIds = $containerIds;
    }

    /**
     * Get the IDs of the Docker containers that were stopped.
     *
     * This method returns an array of container IDs that were stopped
     * by the `docker stop` command executed by the Symfony Process instance.
     *
     * @return ContainerId[] an array of Docker container IDs
     */
    public function getContainerIds()
    {
        return $this->containerIds;
    }
}
