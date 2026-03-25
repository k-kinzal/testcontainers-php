<?php

namespace Testcontainers\Docker\Command;

use Testcontainers\Docker\Exception\DockerException;
use Testcontainers\Docker\Output\DockerVersionOutput;

/**
 * Version command for Docker command.
 *
 * This trait provides a method for retrieving the Docker version information using the `docker version` command.
 */
trait VersionCommand
{
    /**
     * Get Docker version information.
     *
     * This method wraps the `docker version` command to retrieve version information
     * about the Docker client and server.
     *
     * @throws DockerException if the Docker command fails
     *
     * @return DockerVersionOutput the output of the Docker version command
     */
    public function version()
    {
        $process = $this->execute(
            'version',
            null,
            [],
            ['format' => 'json']
        );

        return new DockerVersionOutput($process);
    }

    abstract protected function execute($command, $subcommand = null, $args = [], $options = [], $wait = true);
}
