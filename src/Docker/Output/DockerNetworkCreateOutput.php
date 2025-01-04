<?php

namespace Testcontainers\Docker\Output;

use Symfony\Component\Process\Process;
use Testcontainers\Docker\Types\NetworkId;

/**
 * Represents the output of a Docker `network create` command executed via Symfony Process.
 *
 * This class extends DockerOutput to provide specific handling for network creation.
 */
class DockerNetworkCreateOutput extends DockerOutput
{
    /**
     * The ID of the Docker network.
     *
     * This property holds the network ID of the Docker network that was created
     * by the `docker network create` command executed by the Symfony Process instance.
     *
     * @var NetworkId
     */
    private $networkId;

    /**
     * @param Process $process The Symfony Process instance that executed the `docker network create` command.
     */
    public function __construct($process)
    {
        parent::__construct($process);

        $this->networkId = new NetworkId(trim($process->getOutput()));
    }

    /**
     * Get the Docker network ID.
     *
     * This method returns the ID of the Docker network that was created
     * by the `docker network create` command executed by the Symfony Process instance.
     *
     * @return NetworkId The Docker network ID.
     */
    public function getNetworkId()
    {
        return $this->networkId;
    }
}
