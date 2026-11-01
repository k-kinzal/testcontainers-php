<?php

namespace Testcontainers\Docker\Command;

use Testcontainers\Docker\Output\DockerEventsOutput;
use Testcontainers\Utility\Stringable;

/**
 * Events command for Docker command.
 *
 * This trait provides a method to stream events from the Docker daemon.
 */
trait EventsCommand
{
    /**
     * Stream events from the Docker daemon.
     *
     * This method wraps the `docker events` command without waiting for it to finish.
     *
     * @param array{
     *     filter?: null|string[]|Stringable[],
     *     since?: null|int|string|Stringable,
     *     timeout?: null|float|int,
     *     until?: null|int|string|Stringable,
     * } $options Additional options for the Docker events command, and `timeout` to override the timeout of the client
     * @psalm-param mixed $options
     *
     * @return DockerEventsOutput the output of the Docker events command
     */
    public function events($options = [])
    {
        self::ensureStringKeyedArray($options);

        $hasTimeout = array_key_exists('timeout', $options);
        $timeout = isset($options['timeout']) ? $options['timeout'] : null;
        self::ensureNullableNonNegativeNumber($timeout);

        unset($options['timeout']);

        $options['format'] = '{{json .}}';

        $process = $this->execute(
            'events',
            null,
            [],
            $options,
            false
        );
        if ($hasTimeout) {
            $process->setTimeout($timeout);
        }

        return new DockerEventsOutput($process, !isset($options['until']));
    }

    abstract protected function execute($command, $subcommand = null, $args = [], $options = [], $wait = true);
}
