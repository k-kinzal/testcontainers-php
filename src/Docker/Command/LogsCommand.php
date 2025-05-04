<?php

namespace Testcontainers\Docker\Command;

use Testcontainers\Docker\Output\DockerFollowLogsOutput;
use Testcontainers\Docker\Output\DockerLogsOutput;
use Testcontainers\Docker\Types\ContainerId;
use Testcontainers\Utility\Stringable;

/**
 * Logs command for Docker.
 *
 * This trait provides methods to interact with the logs of Docker containers.
 */
trait LogsCommand
{
    /**
     * Retrieve the logs of a Docker container.
     *
     * This method wraps the `docker logs` command to fetch the logs of the specified container.
     *
     * @param ContainerId $containerId The ID of the container to fetch logs from.
     * @param array{
     *     details?: bool|null,
     *     follow?: bool|null,
     *     since?: string|Stringable|null,
     *     tail?: string|Stringable|null,
     *     timestamps?: bool|null,
     *     until?: string|Stringable|null,
     * } $options Additional options for the Docker logs command.
     * @return DockerFollowLogsOutput|DockerLogsOutput The output containing the logs of the container.
     */
    public function logs($containerId, $options = [])
    {
        $follow = isset($options['follow']) ? $options['follow'] : false;
        $process = $this->execute('logs', null, [(string) $containerId], $options, $follow === false);

        if ($follow === true) {
            return new DockerFollowLogsOutput($process);
        } else {
            return new DockerLogsOutput($process);
        }
    }

    abstract protected function execute($command, $subcommand = null, $args = [], $options = [], $wait = true);
}
