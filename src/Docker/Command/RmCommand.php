<?php

namespace Testcontainers\Docker\Command;

use Testcontainers\Docker\Exception\DockerException;
use Testcontainers\Docker\Exception\NoSuchContainerException;
use Testcontainers\Docker\Output\DockerRmOutput;
use Testcontainers\Docker\Types\ContainerId;

/**
 * Rm command for Docker command.
 *
 * This trait provides a method for removing Docker containers using the `docker rm` command.
 */
trait RmCommand
{
    /**
     * Remove one or more Docker containers.
     *
     * This method wraps the `docker rm` command to remove the specified container(s).
     *
     * @param array|ContainerId|string $containerId the ID or an array of IDs of the container(s) to remove
     * @param array{
     *     force?: null|bool,
     *     link?: null|bool,
     *     volumes?: null|bool,
     * } $options Additional options for the Docker rm command
     *
     * @throws NoSuchContainerException if the specified container does not exist
     * @throws DockerException          if the Docker command fails for any other reason
     *
     * @return DockerRmOutput the output of the Docker rm command, including the removed container IDs
     */
    public function rm($containerId, $options = [])
    {
        if (is_array($containerId)) {
            $containerIds = array_map('strval', $containerId);
        } else {
            $containerIds = [(string) $containerId];
        }

        $process = $this->execute(
            'rm',
            null,
            $containerIds,
            $options
        );

        return new DockerRmOutput($process);
    }

    abstract protected function execute($command, $subcommand = null, $args = [], $options = [], $wait = true);
}
