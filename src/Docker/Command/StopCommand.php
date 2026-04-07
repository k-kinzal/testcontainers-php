<?php

namespace Testcontainers\Docker\Command;

use Testcontainers\Docker\Exception\DockerException;
use Testcontainers\Docker\Exception\NoSuchContainerException;
use Testcontainers\Docker\Output\DockerStopOutput;
use Testcontainers\Docker\Types\ContainerId;
use Testcontainers\Utility\Stringable;

/**
 * Stop command for Docker command.
 *
 * This trait provides a method for stopping one or more running Docker containers using the `docker stop` command.
 */
trait StopCommand
{
    /**
     * Cached stop timeout option key.
     *
     * Determined by the Docker client version:
     * - Docker >= 28.0: 'timeout' (maps to --timeout)
     * - Docker < 28.0: 'time' (maps to --time)
     *
     * @var null|string
     */
    private $stopTimeoutOption;

    /**
     * Stop one or more running Docker containers.
     *
     * This method wraps the `docker stop` command to send a stop signal to the specified container(s) to gracefully stop them.
     *
     * The timeout option key is automatically resolved based on the Docker client version:
     * - Docker >= 28.0 uses `--timeout` (renamed from `--time` in v28.0)
     * - Docker < 28.0 uses `--time`
     *
     * Both `time` and `timeout` are accepted as option keys and normalized internally.
     *
     * @param array<array-key, ContainerId|string>|ContainerId|string $containerId the ID or an array of IDs of the container(s) to stop
     * @param array{
     *     signal?: null|string|Stringable,
     *     time?: null|int,
     *     timeout?: null|int,
     * } $options Additional options for the Docker stop command
     *
     * @throws NoSuchContainerException if the specified container does not exist
     * @throws DockerException          if the Docker command fails for any other reason
     *
     * @return DockerStopOutput the output of the Docker stop command, including the stopped container IDs
     */
    public function stop($containerId, $options = [])
    {
        if (is_array($containerId)) {
            $containerIds = array_map('strval', $containerId);
        } else {
            $containerIds = [(string) $containerId];
        }

        // Normalize timeout option key based on Docker client version
        $timeoutValue = isset($options['timeout']) ? $options['timeout'] : (isset($options['time']) ? $options['time'] : null);
        unset($options['timeout'], $options['time']);
        if ($timeoutValue !== null) {
            if ($this->stopTimeoutOption === null) {
                try {
                    $clientVersion = $this->version()->getClientVersion();
                    $this->stopTimeoutOption = ($clientVersion !== null && version_compare($clientVersion, '28.0', '>=')) ? 'timeout' : 'time';
                } catch (\Exception $e) {
                    $this->stopTimeoutOption = 'time';
                }
            }
            $options[$this->stopTimeoutOption] = $timeoutValue;
        }

        $process = $this->execute(
            'stop',
            null,
            $containerIds,
            $options
        );

        return new DockerStopOutput($process);
    }

    abstract protected function execute($command, $subcommand = null, $args = [], $options = [], $wait = true);

    /**
     * @return \Testcontainers\Docker\Output\DockerVersionOutput
     */
    abstract public function version();
}
