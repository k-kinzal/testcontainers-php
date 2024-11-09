<?php

namespace Testcontainers\Docker;

use Symfony\Component\Process\Process;
use Testcontainers\Docker\Exception\DockerException;
use Testcontainers\Docker\Exception\NoSuchContainerException;
use Testcontainers\Docker\Exception\PortAlreadyAllocatedException;

use function Testcontainers\array_flatten;
use function Testcontainers\kebab;

/**
 * A client for interacting with Docker.
 *
 * This client provides a simple interface for executing Docker commands from PHP.
 * It allows you to run containers, stop containers, and retrieve the status of running containers.
 */
class DockerClient
{
    /**
     * The command used to interact with Docker.
     *
     * @var string The Docker command, default is 'docker'.
     */
    private $command = 'docker';

    /**
    * Global options for Docker commands.
    *
    * These options are applied to all Docker commands executed by this client.
    *
    * @var array
    */
    private $options = [];

    /**
     * The working directory for Docker commands.
     *
     * This directory is used as the current working directory for all Docker commands
     * executed by this client. If set to null, the current PHP process directory is used.
     *
     * @var null|string The working directory path or null if not set.
     */
    private $cwd = null;

    /**
     * Environment variables for Docker commands.
     *
     * These variables are passed to the Docker process when executing commands.
     * If set to null, the current environment variables of the PHP process are used.
     *
     * @var array An associative array of environment variables or null if not set.
     */
    private $env = [];

    /**
     * The input for Docker commands.
     *
     * This can be a stream resource, scalar value, `\Traversable`, or null if no input is provided.
     *
     * @var mixed|null
     */
    private $input = null;

    /**
     * The timeout for Docker commands.
     *
     * This value represents the maximum time in seconds that a Docker command is allowed to run.
     * If set to null, the command will run indefinitely without timing out.
     *
     * @var int|float|null The timeout in seconds, or null to disable the timeout.
     */
    private $timeout = 60;

    /**
     * Options for the `proc_open` function.
     *
     * These options are used when creating a new process using the `proc_open` function.
     * Refer to the PHP documentation for `proc_open` for more details on available options.
     *
     * @var array
     */
    private $proc_options = [];

    /**
     * Set the Docker command path.
     *
     * This method allows you to specify the path to the Docker command
     * that will be used by this client. By default, it is set to 'docker'.
     *
     * @param string $command The path to the Docker command.
     * @return self
     */
    public function withCommand($command)
    {
        $this->command = $command;

        return $this;
    }

    /**
     * Set global Docker options.
     *
     * This method allows you to specify global options that will be applied to all Docker commands
     * executed by this client. These options can include flags and parameters that modify the behavior
     * of Docker commands.
     *
     * @param array $options An associative array of Docker global options.
     * @return self
     */
    public function withGlobalOptions($options)
    {
        $this->options = $options;

        return $this;
    }

    /**
     * Set the working directory for Docker commands.
     *
     * This method sets the directory that will be used as the current working directory
     * for all Docker commands executed by this client. If not set, the current PHP process
     * directory will be used.
     *
     * @param null|string $cwd The path to the working directory.
     * @return self
     */
    public function withCwd($cwd)
    {
        $this->cwd = $cwd;

        return $this;
    }

    /**
     * Set environment variables for Docker commands.
     *
     * This method allows you to specify environment variables that will be passed
     * to the Docker process when executing commands. If not set, the current
     * environment variables of the PHP process will be used.
     *
     * @param array $env An associative array of environment variables.
     * @return self
     */
    public function withEnv($env)
    {
        $this->env = $env;

        return $this;
    }

    /**
     * Set the input for Docker commands.
     *
     * This method allows you to specify the input that will be passed to the Docker process
     * when executing commands. The input can be a stream resource, a scalar value, a `\Traversable`,
     * or `null` if no input is provided.
     *
     * @param null|mixed $input The input for the Docker process.
     * @return self
     */
    public function withInput($input)
    {
        $this->input = $input;

        return $this;
    }

    /**
     * Set the timeout for Docker commands.
     *
     * This method allows you to specify the maximum time in seconds that a Docker command is allowed to run.
     * If set to `null`, the command will run indefinitely without timing out.
     *
     * @param int|float|null $timeout The timeout in seconds, or `null` to disable the timeout.
     * @return self
     */
    public function withTimeout($timeout)
    {
        $this->timeout = $timeout;

        return $this;
    }

    /**
     * Set options for the `proc_open` function.
     *
     * This method allows you to specify options that will be used when creating a new process
     * using the `proc_open` function. Refer to the PHP documentation for `proc_open` for more details
     * on available options.
     *
     * @param array $proc_options An associative array of options for `proc_open`.
     * @return self
     */
    public function withProcOptions($proc_options)
    {
        $this->proc_options = $proc_options;

        return $this;
    }

    /**
     * Create and run a new container from a Docker image.
     *
     * This method wraps the `docker run` command to create and run a new container from a specified Docker image.
     *
     * @param string $image The name of the Docker image to use.
     * @param string|null $command The command to run inside the container (optional).
     * @param array|null $args The arguments for the command (optional).
     * @param array $options Additional options for the Docker command.
     * @return DockerRunOutput|DockerRunWithDetachOutput The output of the Docker run command. If the `detach` option is set to `true`, a `DockerRunWithDetachOutput` object is returned.
     *
     * @throws PortAlreadyAllocatedException If the specified port is already allocated.
     * @throws DockerException If the Docker command fails.
     */
    public function run($image, $command = null, $args = null, $options = [])
    {
        $commandline = array_filter(array_flatten([
            $this->command,
            $this->arrayToArgs($this->options),
            'run',
            $this->arrayToArgs($options),
            $image,
            $command,
            $args,
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
            if (PortAlreadyAllocatedException::match($stderr)) {
                throw new PortAlreadyAllocatedException($process);
            }
            throw new DockerException($process);
        }

        if (isset($options['detach']) && $options['detach'] === true) {
            $stdout = $process->getOutput();
            $containerId = trim($stdout);
            return new DockerRunWithDetachOutput($process, $containerId);
        } else {
            return new DockerRunOutput($process);
        }
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
     * Convert array to command line arguments
     *
     * @param array $options command line options (key-value pairs)
     * @return array
     */
    private function arrayToArgs($options)
    {
        $result = [];
        foreach ($options as $key => $value) {
            $key = kebab($key);
            if ($value === null) {
                continue;
            }
            if ($value === false) {
                continue;
            }
            if ($value === true) {
                $result[] = "--$key";
                continue;
            }
            if (is_scalar($value)) {
                $result[] = "--$key";
                $result[] = $value;
                continue;
            }
            if (is_array($value)) {
                foreach ($value as $k => $v) {
                    $result[] = "--$key";
                    if (is_string($k)) {
                        $result[] = "$k=$v";
                    } elseif (is_scalar($v) && !is_bool($v)) {
                        $result[] = $v;
                    }
                }
            }
        }

        return $result;
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
