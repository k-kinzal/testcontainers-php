<?php

namespace Testcontainers\Docker\Command;

use Testcontainers\Docker\Exception\BindAddressAlreadyUseException;
use Testcontainers\Docker\Exception\DockerException;
use Testcontainers\Docker\Exception\NoSuchContainerException;
use Testcontainers\Docker\Exception\NoSuchObjectException;
use Testcontainers\Docker\Exception\PortAlreadyAllocatedException;
use Testcontainers\Docker\Output\DockerRunOutput;
use Testcontainers\Docker\Output\DockerRunWithDetachOutput;
use Testcontainers\Utility\Stringable;

/**
 * Run command for Docker commands.
 *
 * This trait provides functionality for creating and running a new container from a Docker image.
 */
trait RunCommand
{
    /**
     * Create and run a new container from a Docker image.
     *
     * This method wraps the `docker run` command to create and run a new container from a specified Docker image.
     *
     * @param string      $image   the name of the Docker image to use
     * @param null|string $command the command to run inside the container (optional)
     * @param array       $args    the arguments for the command (optional)
     * @param array{
     *     addHost?: string[]|Stringable[]|null,
     *     detach?: bool|null,
     *     env?: array<string, string|Stringable>|null,
     *     label?: array<string, string|Stringable>|null,
     *     mount?: string[]|Stringable[]|null,
     *     name?: string|null,
     *     network?: string|Stringable|null,
     *     networkAlias?: string[]|null,
     *     publish?: string[]|null,
     *     pull?: string|Stringable|null,
     *     privileged?: bool|null,
     *     quiet?: bool|null,
     *     volumesFrom?: string[]|Stringable[]|null,
     *     workdir?: string|null,
     * } $options Additional options for the Docker command
     *
     * @return DockerRunOutput|DockerRunWithDetachOutput The output of the Docker run command. If the `detach` option is set to `true`, a `DockerRunWithDetachOutput` object is returned.
     *
     * @throws NoSuchContainerException       if the specified container does not exist
     * @throws NoSuchObjectException          if the specified object does not exist
     * @throws PortAlreadyAllocatedException  if the specified port is already allocated
     * @throws BindAddressAlreadyUseException if the specified bind address is already in use
     * @throws DockerException                if the Docker command fails
     */
    public function run($image, $command = null, $args = [], $options = [])
    {
        $process = $this->execute(
            'run',
            null,
            array_filter(array_merge([$image, $command], $args)),
            $options
        );
        if (isset($options['detach']) && true === $options['detach']) {
            return new DockerRunWithDetachOutput($process);
        }

        return new DockerRunOutput($process);
    }

    abstract protected function execute($command, $subcommand = null, $args = [], $options = [], $wait = true);
}
