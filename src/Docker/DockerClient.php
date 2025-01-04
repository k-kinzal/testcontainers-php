<?php

namespace Testcontainers\Docker;

use Symfony\Component\Process\Process;
use Testcontainers\Docker\Command\BaseCommand;
use Testcontainers\Docker\Command\RunCommand;
use Testcontainers\Docker\Exception\DockerException;
use Testcontainers\Docker\Exception\InvalidValueException;
use Testcontainers\Docker\Exception\NoSuchContainerException;
use Testcontainers\Docker\Exception\NoSuchObjectException;

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

    /**
     * Inspect a Docker container.
     *
     * This method wraps the `docker inspect` command to retrieve detailed information about the specified container.
     *
     * @param string $containerId The ID of the container to inspect.
     * @return DockerInspectOutput The output of the Docker inspect command, including detailed information about the container.
     *
     * @throws DockerException If the Docker command fails for any other reason.
     * @throws NoSuchObjectException If the specified container does not exist.
     * @throws InvalidValueException If the output of the `docker inspect` command is not valid JSON.
     */
    public function inspect($containerId)
    {
        $commandline = array_filter(array_flatten([
            $this->command,
            $this->arrayToArgs($this->options),
            'inspect',
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
            if (NoSuchObjectException::match($process->getErrorOutput())) {
                throw new NoSuchObjectException($process);
            }
            throw new DockerException($process);
        }

        return new DockerInspectOutput($process);
    }

    /**
     * Stop one or more running Docker containers.
     *
     * This method wraps the `docker stop` command to send a stop signal to the specified container(s) to gracefully stop them.
     *
     * @param string|array $containerId The ID or an array of IDs of the container(s) to stop.
     * @param array $options Additional options for the Docker stop command.
     * @return DockerStopOutput The output of the Docker stop command, including the stopped container IDs.
     *
     * @throws NoSuchContainerException If the specified container does not exist.
     * @throws DockerException If the Docker command fails for any other reason.
     */
    public function stop($containerId, $options = [])
    {
        $containerIds = [];
        if (is_array($containerId)) {
            $containerIds = $containerId;
        } else {
            $containerIds[] = $containerId;
        }

        $commandline = array_filter(array_flatten([
            $this->command,
            $this->arrayToArgs($this->options),
            'stop',
            $this->arrayToArgs($options),
            $containerIds,
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

        $stdout = $process->getOutput();
        $containerIds = explode("\n", trim($stdout));

        return new DockerStopOutput($process, $containerIds);
    }

    /**
     * Get the status of Docker processes.
     *
     * This method wraps the `docker ps` command to retrieve the status of running Docker containers.
     *
     * @param array $options Additional options for the Docker ps command.
     * @return DockerProcessStatusOutput The output of the Docker ps command, including the status of running containers.
     *
     * @throws DockerException If the Docker command fails.
     */
    public function processStatus($options = [])
    {
        $commandline = array_filter(array_flatten([
            $this->command,
            $this->arrayToArgs($this->options),
            'ps',
            $this->arrayToArgs(array_merge($options, [
                'format' => 'json'
            ])),
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

        $out = $process->getOutput();
        $statuses = [];
        foreach (explode("\n", trim($out)) as $line) {
            if (empty($line)) {
                continue;
            }
            $status = json_decode($line, true);
            if ($status === null) {
                throw new DockerException($process);
            }
            $statuses[] = $status;
        }

        return new DockerProcessStatusOutput($process, $statuses);
    }

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
