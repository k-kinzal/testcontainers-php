<?php

namespace Testcontainers\Docker\Output;

use Symfony\Component\Process\Process;
use Testcontainers\Docker\Types\ContainerId;

use function Testcontainers\ensure;

/**
 * Represents the output of a Docker `rm` command executed via Symfony Process.
 *
 * This class extends DockerOutput to include the container IDs of the Docker containers that were removed.
 */
class DockerRmOutput extends DockerOutput
{
    /**
     * An array of Docker container IDs.
     *
     * This property holds the IDs of the Docker containers that were removed
     * by the `docker rm` command executed by the Symfony Process instance.
     *
     * @var ContainerId[]
     */
    private $containerIds;

    /**
     * @param Process $process the Symfony Process instance that executed the `docker rm` command
     */
    public function __construct($process)
    {
        /** @psalm-suppress RedundantConditionGivenDocblockType */
        ensure($process instanceof Process, '$process must be Process');

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
     * Get the IDs of the Docker containers that were removed.
     *
     * This method returns an array of container IDs that were removed
     * by the `docker rm` command executed by the Symfony Process instance.
     *
     * @return ContainerId[] an array of Docker container IDs
     */
    public function getContainerIds()
    {
        return $this->containerIds;
    }
}
