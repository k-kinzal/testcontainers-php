<?php

namespace Testcontainers\Docker;

use Symfony\Component\Process\Process;
use Testcontainers\Docker\Command\BaseCommand;
use Testcontainers\Docker\Command\InspectCommand;
use Testcontainers\Docker\Command\LogsCommand;
use Testcontainers\Docker\Command\RunCommand;
use Testcontainers\Docker\Command\StopCommand;
use Testcontainers\Docker\Exception\DockerException;
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
    use InspectCommand;
    use LogsCommand;
    use RunCommand;
    use StopCommand;

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
