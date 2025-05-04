<?php

namespace Testcontainers\Docker\Command;

use LogicException;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\Process\Process;
use Testcontainers\Docker\Exception\BindAddressAlreadyUseException;
use Testcontainers\Docker\Exception\DockerException;
use Testcontainers\Docker\Exception\NoSuchContainerException;
use Testcontainers\Docker\Exception\NoSuchObjectException;
use Testcontainers\Docker\Exception\PortAlreadyAllocatedException;
use Testcontainers\Environments;
use function Testcontainers\kebab;

/**
 * Base command for Docker commands.
 *
 * This trait provides common functionality for executing Docker commands using the Symfony Process component.
 * It allows you to set the Docker command path, global options, working directory, environment variables,
 * input, timeout, and process options for Docker commands.
 */
trait BaseCommand
{
    /**
     * The command used to interact with Docker.
     *
     * @var string the Docker command, default is 'docker'
     */
    private $command = 'docker';

    /**
     * Global options for Docker commands.
     *
     * These options are applied to all Docker commands executed by this client.
     *
     * @var array{
     *             host?: string|string[],
     *             }
     */
    private $options = [];

    /**
     * The working directory for Docker commands.
     *
     * This directory is used as the current working directory for all Docker commands
     * executed by this client. If set to null, the current PHP process directory is used.
     *
     * @var null|string the working directory path or null if not set
     */
    private $cwd;

    /**
     * Environment variables for Docker commands.
     *
     * These variables are passed to the Docker process when executing commands.
     * If set to null, the current environment variables of the PHP process are used.
     *
     * @var array<string, string> an associative array of environment variables or null if not set
     */
    private $env = [];

    /**
     * The input for Docker commands.
     *
     * This can be a stream resource, scalar value, `\Traversable`, or null if no input is provided.
     *
     * @var null|mixed
     */
    private $input;

    /**
     * The timeout for Docker commands.
     *
     * This value represents the maximum time in seconds that a Docker command is allowed to run.
     * If set to null, the command will run indefinitely without timing out.
     *
     * @var null|float|int the timeout in seconds, or null to disable the timeout
     */
    private $timeout = 60;

    /**
     * Options for the `proc_open` function.
     *
     * These options are used when creating a new process using the `proc_open` function.
     * Refer to the PHP documentation for `proc_open` for more details on available options.
     *
     * @var array<string, string>
     */
    private $proc_options = [];

    /**
     * Logger instance.
     *
     * @var null|LoggerInterface
     */
    private $logger;

    /**
     * Set the Docker command path.
     *
     * This method allows you to specify the path to the Docker command
     * that will be used by this client. By default, it is set to 'docker'.
     *
     * @param string $command the path to the Docker command
     *
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
     * @param array<string, string> $options an associative array of Docker global options
     *
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
     * @param null|string $cwd the path to the working directory
     *
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
     * @param array<string, string> $env an associative array of environment variables
     *
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
     * @param null|mixed $input the input for the Docker process
     *
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
     * @param null|float|int $timeout the timeout in seconds, or `null` to disable the timeout
     *
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
     * @param array<string, string> $proc_options an associative array of options for `proc_open`
     *
     * @return self
     */
    public function withProcOptions($proc_options)
    {
        $this->proc_options = $proc_options;

        return $this;
    }

    /**
     * Set the logger instance.
     *
     * @param LoggerInterface $logger
     *
     * @return $this
     */
    public function withLogger($logger)
    {
        $this->logger = $logger;

        return $this;
    }

    /**
     * Get the Docker host.
     *
     * @return string
     */
    public function getHost()
    {
        // Check if the host is set in the global options
        $host = isset($this->options['host']) ? $this->options['host'] : null;
        if (is_array($host)) {
            return $host[0];
        }
        if (is_string($host)) {
            return $host;
        }
        // Check if the host is set in the environment variables
        $host = isset($this->env['DOCKER_HOST']) ? $this->env['DOCKER_HOST'] : null;
        if (is_string($host)) {
            return $host;
        }
        // Check if the host is set in the DOCKER_HOST environment variable
        $host = Environments::DOCKER_HOST();
        if ($host) {
            return $host;
        }

        return 'unix:///var/run/docker.sock';
    }

    /**
     * Expand environment variables in a string.
     *
     * This method expands environment variables in a string using the current environment variables
     * of the PHP process. If the `$env` parameter is set, it will use the specified environment variables
     * instead of the current PHP process environment variables.
     *
     * @param string $s the string to expand
     *
     * @return string The expanded string. If no environment variables are found, the original string is returned.
     */
    public function expandEnv($s)
    {
        $env = $this->env;

        $expanded = preg_replace_callback('/\$\{([a-zA-Z_][a-zA-Z0-9_]*)}/', function ($m) use ($env) {
            if (empty($env)) {
                $v = getenv($m[1]);

                return false !== $v ? $v : $m[0];
            }

            return isset($env[$m[1]]) ? $env[$m[1]] : $m[0];
        }, $s);

        return null !== $expanded ? $expanded : $s;
    }

    /**
     * Get the logger instance.
     *
     * @return LoggerInterface
     */
    protected function logger()
    {
        if (null === $this->logger) {
            return new NullLogger();
        }

        return $this->logger;
    }

    /**
     * Execute a Docker command.
     *
     * @param string               $command    the command to execute
     * @param null|string          $subcommand the subcommand to execute (optional)
     * @param string[]             $args       the arguments for the command (optional)
     * @param array<string, mixed> $options    additional options for the Docker command
     * @param bool                 $wait       whether to wait for the command to finish executing
     *
     * @throws NoSuchContainerException       if the specified container does not exist
     * @throws NoSuchObjectException          if the specified object does not exist
     * @throws PortAlreadyAllocatedException  if the specified port is already allocated
     * @throws BindAddressAlreadyUseException if the specified bind address is already in use
     * @throws DockerException                if the Docker command fails
     *
     * @return Process The Symfony Process instance that was executed
     */
    protected function execute($command, $subcommand = null, $args = [], $options = [], $wait = true)
    {
        // docker [global options] command [subcommand] [options] [args]
        $commandLine = ['docker'];
        if (count($this->options) > 0) {
            $commandLine = array_merge($commandLine, $this->arrayToArgs($this->options));
        }
        $commandLine[] = $command;
        if ($subcommand) {
            $commandLine[] = $subcommand;
        }
        if (count($options) > 0) {
            $commandLine = array_merge($commandLine, $this->arrayToArgs($options));
        }
        if (count($args) > 0) {
            $commandLine = array_merge($commandLine, $args);
        }
        $this->logger()->debug(implode(' ', $commandLine), [
            'cwd' => $this->cwd,
            'env' => $this->env,
            'input' => $this->input,
            'timeout' => $this->timeout,
            'proc_options' => $this->proc_options,
        ]);
        $process = new Process(
            $commandLine,
            $this->cwd,
            $this->env,
            $this->input,
            $this->timeout,
            $this->proc_options
        );
        if ($wait) {
            $process->run();
            if (!$process->isSuccessful()) {
                $stderr = $process->getErrorOutput();
                if (NoSuchContainerException::match($stderr)) {
                    throw new NoSuchContainerException($process);
                }
                if (NoSuchObjectException::match($stderr)) {
                    throw new NoSuchObjectException($process);
                }
                if (PortAlreadyAllocatedException::match($stderr)) {
                    throw new PortAlreadyAllocatedException($process);
                }
                if (BindAddressAlreadyUseException::match($stderr)) {
                    throw new BindAddressAlreadyUseException($process);
                }

                throw new DockerException($process);
            }
        } else {
            $process->start();
        }

        return $process;
    }

    /**
     * Convert array to command line arguments.
     *
     * @param array<string, mixed> $options command line options (key-value pairs)
     *
     * @return array
     */
    private function arrayToArgs($options)
    {
        $result = [];
        foreach ($options as $key => $value) {
            $key = kebab($key);
            if (null === $value) {
                continue;
            }
            if (false === $value) {
                continue;
            }
            if (true === $value) {
                $result[] = "--{$key}";

                continue;
            }
            if (is_scalar($value)) {
                $result[] = "--{$key}";
                if (is_string($value)) {
                    $result[] = $this->expandEnv($value);
                } else {
                    $result[] = $value;
                }

                continue;
            }
            if (is_object($value) && method_exists($value, '__toString')) {
                $result[] = "--{$key}";
                $result[] = $this->expandEnv((string) $value);

                continue;
            }
            if (is_array($value)) {
                foreach ($value as $k => $v) {
                    $result[] = "--{$key}";
                    if (is_string($k)) {
                        if (is_string($v)) {
                            $result[] = $k.'='.$this->expandEnv($v);
                        } elseif (is_scalar($v)) {
                            $result[] = $k.'='.$v;
                        } elseif (is_object($v) && method_exists($v, '__toString')) {
                            $result[] = $k.'='.$this->expandEnv((string) $v);
                        } else {
                            throw new LogicException('Unsupported value type: `'.var_export($v, true).'`');
                        }
                    } else {
                        if (is_string($v)) {
                            $result[] = $this->expandEnv($v);
                        } elseif (is_scalar($v)) {
                            $result[] = $v;
                        } elseif (is_object($v) && method_exists($v, '__toString')) {
                            $result[] = $this->expandEnv((string) $v);
                        } else {
                            throw new LogicException('Unsupported value type: `'.var_export($v, true).'`');
                        }
                    }
                }
            }
        }

        return $result;
    }
}
