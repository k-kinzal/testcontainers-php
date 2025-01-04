<?php

namespace Testcontainers\Docker\Command;

use Testcontainers\Docker\Exception\DockerException;
use Testcontainers\Docker\Exception\NoSuchObjectException;
use Testcontainers\Docker\Output\DockerInspectOutput;
use Testcontainers\Docker\Types\ContainerId;

/**
 * Inspect command for Docker command.
 *
 * This trait provides a method for inspecting a Docker container using the `docker inspect` command.
 */
trait InspectCommand
{
    /**
     * Inspect a Docker container.
     *
     * This method wraps the `docker inspect` command to retrieve detailed information about the specified container.
     *
     * @param ContainerId $containerId The ID of the container to inspect.
     * @return DockerInspectOutput The output of the Docker inspect command, including detailed information about the container.
     *
     * @throws DockerException If the Docker command fails for any other reason.
     * @throws NoSuchObjectException If the specified container does not exist.
     */
    public function inspect($containerId)
    {
        $process = $this->execute('inspect', null, [(string) $containerId], [
            'format' => 'json',
        ]);
        return new DockerInspectOutput($process);
    }

    abstract protected function execute($command, $subcommand = null, $args = [], $options = [], $wait = true);
}
