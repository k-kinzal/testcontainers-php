<?php

namespace Testcontainers\Docker\Command;

use Testcontainers\Docker\Output\DockerNetworkCreateOutput;

/**
 * Network create command for Docker.
 *
 * This trait provides methods to create a network using the `docker network create` command.
 */
trait NetworkCreateCommand
{
    /**
     * Create a new Docker network.
     *
     * This method wraps the `docker network create` command to create a new Docker network.
     *
     * @param string $network The name of the Docker network to create.
     * @param array $options Additional options for the Docker network create command.
     * @return DockerNetworkCreateOutput The output of the Docker network create command.
     */
    public function networkCreate($network, $options = [])
    {
        $process = $this->execute(
            'network',
            'create',
            [$network],
            $options
        );

        return new DockerNetworkCreateOutput($process);
    }

    abstract protected function execute($command, $subcommand = null, $args = [], $options = [], $wait = true);
}
