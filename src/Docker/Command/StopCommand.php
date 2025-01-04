<?php

namespace Testcontainers\Docker\Command;

use Testcontainers\Docker\Exception\DockerException;
use Testcontainers\Docker\Exception\NoSuchContainerException;
use Testcontainers\Docker\Output\DockerStopOutput;
use Testcontainers\Docker\Types\ContainerId;

/**
 * Stop command for Docker command.
 *
 * This trait provides a method for stopping one or more running Docker containers using the `docker stop` command.
 */
trait StopCommand
{
    /**
     * Stop one or more running Docker containers.
     *
     * This method wraps the `docker stop` command to send a stop signal to the specified container(s) to gracefully stop them.
     *
     * @param ContainerId|string|array $containerId The ID or an array of IDs of the container(s) to stop.
     * @param array $options Additional options for the Docker stop command.
     * @return DockerStopOutput The output of the Docker stop command, including the stopped container IDs.
     *
     * @throws NoSuchContainerException If the specified container does not exist.
     * @throws DockerException If the Docker command fails for any other reason.
     */
    public function stop($containerId, $options = [])
    {
        if (is_array($containerId)) {
            $containerIds = array_map('strval', $containerId);
        } else {
            $containerIds = [(string) $containerId];
        }

        $process = $this->execute(
            'stop',
            null,
            $containerIds,
            $options
        );

        return new DockerStopOutput($process);
    }

    abstract protected function execute($command, $subcommand = null, $args = [], $options = []);
}
