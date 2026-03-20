<?php

namespace Testcontainers\Docker\Command;

use Testcontainers\Docker\Exception\DockerException;
use Testcontainers\Docker\Exception\NoSuchContainerException;
use Testcontainers\Docker\Output\DockerOutput;
use Testcontainers\Docker\Types\ContainerId;
use Testcontainers\Utility\Stringable;

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
     * @param array|ContainerId|string $containerId the ID or an array of IDs of the container(s) to remove
     * @param array{
     *     force?: null|bool,
     * } $options Additional options for the Docker rm command
     *
     * @throws NoSuchContainerException if the specified container does not exist
     * @throws DockerException          if the Docker command fails for any other reason
     *
     * @return DockerOutput the output of the Docker rm command
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

        return new DockerOutput($process);
    }

    abstract protected function execute($command, $subcommand = null, $args = [], $options = [], $wait = true);
}
