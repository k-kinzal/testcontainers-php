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
     *     addHost?: null|string[]|Stringable[],
     *     detach?: null|bool,
     *     env?: null|array<string, string|Stringable>,
     *     label?: null|array<string, string|Stringable>,
     *     mount?: null|string[]|Stringable[],
     *     name?: null|string,
     *     network?: null|string|Stringable,
     *     networkAlias?: null|string[],
     *     publish?: null|string[],
     *     pull?: null|string|Stringable,
     *     privileged?: null|bool,
     *     quiet?: null|bool,
     *     volumesFrom?: null|string[]|Stringable[],
     *     workdir?: null|string,
     * } $options Additional options for the Docker command
     *
     * @throws NoSuchContainerException       if the specified container does not exist
     * @throws NoSuchObjectException          if the specified object does not exist
     * @throws PortAlreadyAllocatedException  if the specified port is already allocated
     * @throws BindAddressAlreadyUseException if the specified bind address is already in use
     * @throws DockerException                if the Docker command fails
     *
     * @return DockerRunOutput|DockerRunWithDetachOutput The output of the Docker run command. If the `detach` option is set to `true`, a `DockerRunWithDetachOutput` object is returned.
     */
    public function run($image, $command = null, $args = [], $options = [])
    {
        $process = $this->execute(
            'run',
            null,
            array_filter(array_merge([$image, $command], $args)),
            $options
        );
        if (isset($options['detach']) && $options['detach'] === true) {
            return new DockerRunWithDetachOutput($process);
        }

        return new DockerRunOutput($process);
    }

    abstract protected function execute($command, $subcommand = null, $args = [], $options = [], $wait = true);
}
