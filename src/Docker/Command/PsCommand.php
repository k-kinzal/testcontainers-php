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
     * This method wraps the `docker ps` command to list containers.
     * Output is always parsed as JSON via `--format json`.
     *
     * @param array{
     *     all?: null|bool,
     *     filter?: null|string[],
     *     last?: null|int,
     *     latest?: null|bool,
     *     noTrunc?: null|bool,
     *     quiet?: null|bool,
     *     size?: null|bool,
     * } $options Additional options for the Docker ps command
     *
     * @throws DockerException if the Docker command fails
     *
     * @return DockerPsOutput the output of the Docker ps command
     */
    public function ps($options = [])
    {
        // Force JSON format for structured parsing
        $options['format'] = 'json';

        // Use --no-trunc to get full container IDs
        if (!isset($options['noTrunc'])) {
            $options['noTrunc'] = true;
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
