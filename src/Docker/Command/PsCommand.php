<?php

namespace Testcontainers\Docker\Command;

use Testcontainers\Docker\Exception\DockerException;
use Testcontainers\Docker\Output\DockerPsOutput;

/**
 * Ps command for Docker command.
 *
 * This trait provides a method for listing Docker containers using the `docker ps` command.
 */
trait PsCommand
{
    /**
     * List Docker containers.
     *
     * @param array{
     *     all?: null|bool,
     *     filter?: null|string[],
     *     format?: null|string,
     * } $options Additional options for the Docker ps command
     *
     * @throws DockerException if the Docker command fails
     *
     * @return DockerPsOutput the output of the Docker ps command
     */
    public function ps($options = [])
    {
        if (!isset($options['format'])) {
            $options['format'] = '{{.ID}}\t{{.Labels}}';
        }
        if (!isset($options['all'])) {
            $options['all'] = true;
        }

        $process = $this->execute(
            'ps',
            null,
            [],
            $options
        );

        return new DockerPsOutput($process);
    }

    abstract protected function execute($command, $subcommand = null, $args = [], $options = [], $wait = true);
}
