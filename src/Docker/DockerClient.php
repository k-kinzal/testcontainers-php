<?php

namespace Testcontainers\Docker;

use Symfony\Component\Process\Process;
use Testcontainers\Docker\Command\BaseCommand;
use Testcontainers\Docker\Command\InspectCommand;
use Testcontainers\Docker\Command\RunCommand;
use Testcontainers\Docker\Command\StopCommand;
use Testcontainers\Docker\Exception\DockerException;
use Testcontainers\Docker\Exception\NoSuchContainerException;
use Testcontainers\Docker\Output\DockerFollowLogsOutput;
use Testcontainers\Docker\Output\DockerLogsOutput;
use Testcontainers\Docker\Output\DockerNetworkCreateOutput;

use function Testcontainers\array_flatten;

/**
 * A client for interacting with Docker.
 *
 * This client provides a simple interface for executing Docker commands from PHP.
 * It allows you to run containers, stop containers, and retrieve the status of running containers.
 */
class DockerClient
{
    use BaseCommand;
    use RunCommand;
    use InspectCommand;
    use StopCommand;

    /**
     * Retrieve the logs of a Docker container.
     *
     * This method wraps the `docker logs` command to fetch the logs of the specified container.
     *
     * @param string $containerId The ID of the container to fetch logs from.
     * @param array $options Additional options for the Docker logs command.
     * @return DockerLogsOutput The output containing the logs of the container.
     */
    public function logs($containerId, $options = [])
    {
        $commandline = array_filter(array_flatten([
            $this->command,
            $this->arrayToArgs($this->options),
            'logs',
            $this->arrayToArgs($options),
            $containerId,
        ]));
        $process = new Process(
            $commandline,
            $this->cwd,
            $this->env,
            $this->input,
            $this->timeout,
            $this->proc_options
        );
        $process->run();

        if (!$process->isSuccessful()) {
            $stderr = $process->getErrorOutput();
            if (NoSuchContainerException::match($stderr)) {
                throw new NoSuchContainerException($process);
            }
            throw new DockerException($process);
        }

        return new DockerLogsOutput($process);
    }

    /**
     * Follow the logs of a Docker container.
     *
     * This method wraps the `docker logs` command with the `--follow` option to stream the logs
     * of the specified container in real-time.
     *
     * @param string $containerId The ID of the container to follow logs from.
     * @param array $options Additional options for the Docker logs command.
     * @return DockerFollowLogsOutput The output containing the streamed logs of the container.
     */
    public function followLogs($containerId, $options = [])
    {
        $commandline = array_filter(array_flatten([
            $this->command,
            $this->arrayToArgs($this->options),
            'logs',
            $this->arrayToArgs(array_merge($options, [
                'follow' => true,
            ])),
            $containerId
        ]));
        $process = new Process(
            $commandline,
            $this->cwd,
            $this->env,
            $this->input,
            $this->timeout,
            $this->proc_options
        );
        $process->start();

        return new DockerFollowLogsOutput($process);
    }

    /**
     * Create a new Docker network.
     *
     * This method wraps the `docker network create` command to create a new Docker network.
     *
     * @param string $network The name of the Docker network to create.
     * @param array $options Additional options for the Docker network create command.
     * @return DockerNetworkCreateOutput The output of the Docker network create command.
     */
    public function networkCreate($network, $options = [])
    {
        $commandline = array_filter(array_flatten([
            $this->command,
            $this->arrayToArgs($this->options),
            'network',
            'create',
            $this->arrayToArgs($options),
            $network,
        ]));
        $process = new Process(
            $commandline,
            $this->cwd,
            $this->env,
            $this->input,
            $this->timeout,
            $this->proc_options
        );
        $process->run();

        if (!$process->isSuccessful()) {
            throw new DockerException($process);
        }

        return new DockerNetworkCreateOutput($process);
    }

    public function __clone()
    {
        return (new self())
            ->withCommand($this->command)
            ->withGlobalOptions($this->options)
            ->withCwd($this->cwd)
            ->withEnv($this->env)
            ->withInput($this->input)
            ->withTimeout($this->timeout)
            ->withProcOptions($this->proc_options);
    }
}
