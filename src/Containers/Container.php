<?php

namespace Testcontainers\Containers;

use Testcontainers\Containers\PortStrategy\PortStrategy;
use Testcontainers\Containers\StartupCheckStrategy\StartupCheckStrategy;
use Testcontainers\Containers\Types\BindMode;
use Testcontainers\Containers\Types\HostToIp;
use Testcontainers\Containers\Types\ImagePullPolicy;
use Testcontainers\Containers\WaitStrategy\WaitStrategy;

/**
 * An interface representing the definition of a container.
 */
interface Container
{
    /**
     * Set the name for this container, similar to the `--name <name>` option on the Docker CLI.
     *
     * @param string $name The name to set.
     * @return self
     */
    public function withName($name);

    /**
     * Adds a file system binding to the container.
     *
     * @param string $hostPath The path on the host machine.
     * @param string $containerPath The path inside the container.
     * @param BindMode $mode The mode of the bind (e.g., read-only or read-write).
     * @return self
     */
    public function withFileSystemBind($hostPath, $containerPath, $mode);

    /**
     * Adds container volumes to the current container instance.
     *
     * @param ContainerInstance $container The container instance from which to add volumes.
     * @param BindMode $mode The mode of the bind (e.g., read-only or read-write).
     * @return self
     */
    public function withVolumesFrom($container, $mode);

    /**
     * Set the ports that this container listens on.
     *
     * @param array|int|string $ports The ports to expose. Can be a single port, a range of ports, or an array of ports.
     * @return self
     */
    public function withExposedPorts($ports);

    /**
     * Add an environment variable to the container.
     *
     * @param string $key The name of the environment variable.
     * @param string $value The value of the environment variable.
     * @return self
     */
    public function withEnv($key, $value);

    /**
     * Add multiple environment variables to the container.
     *
     * @param array<string, string> $env An associative array where the key is the environment variable name and the value is the environment variable value.
     * @return self
     */
    public function withEnvs($env);

    /**
     * Add a label to the container.
     *
     * @param string $key The name of the label.
     * @param string $value The value of the label.
     * @return self
     */
    public function withLabel($key, $value);

    /**
     * Adds multiple labels to the container.
     *
     * @param array<string, string> $labels An associative array where the key is the label name and the value is the label value.
     * @return self
     */
    public function withLabels($labels);

    /**
     * Sets the command to be executed in the container.
     *
     * @param string $cmd The command to run inside the container.
     * @return self
     */
    public function withCommand($cmd);

    /**
    * Set the command that should be run in the container.
    *
    * @param string[] $commandParts The parts of the command to run inside the container.
    * @return self
    */
    public function withCommands($commandParts);

    /**
     * Add an extra host entry to be passed to the container.
     *
     * @param HostToIp|string|array $hostname The hostname to add.
     * @param null|string $ipAddress The IP address associated with the hostname.
     * @return self
     */
    public function withExtraHost($hostname, $ipAddress = null);

    /**
     * Add multiple extra host entries to be passed to the container.
     *
     * @param HostToIp[]|string[]|array{
     *      hostname: string,
     *      ipAddress: string
     *  }[] $extraHosts The extra hosts to add.
     * @return self
     */
    public function withExtraHosts($extraHosts);

    /**
     * Set the network mode for this container, similar to the `--net <name>` option on the Docker CLI.
     *
     * @param string $networkMode The network mode, e.g., 'host', 'bridge', 'none', or the name of an existing named network.
     * @return self
     */
    public function withNetworkMode($networkMode);

    /**
     * Set the network aliases for this container, similar to the `--network-alias <my-service>` option on the Docker CLI.
     *
     * @param string[] $aliases The network aliases to set.
     * @return self
     */
    public function withNetworkAliases($aliases);

    /**
     * Set the image pull policy of the container.
     *
     * @param ImagePullPolicy $policy The image pull policy to set.
     * @return self
     */
    public function withImagePullPolicy($policy);

    /**
     * Set the working directory that the container should use on startup.
     *
     * @param string $workDir The path to the working directory inside the container.
     * @return self
     */
    public function withWorkingDirectory($workDir);

    /**
     * Set the duration of waiting time until the container is treated as started.
     *
     * @param int $timeout The duration to wait.
     * @return self
     */
    public function withStartupTimeout($timeout);

    /**
     * Set the privileged mode for the container.
     *
     * @param boolean $mode Whether to enable privileged mode.
     * @return self
     */
    public function withPrivilegedMode($mode);

    /**
     * Set the startup check strategy used for checking whether the container has started.
     *
     * @param StartupCheckStrategy $strategy The startup check strategy to use.
     * @return self
     */
    public function withStartupCheckStrategy($strategy);

    /**
     * Set the port strategy used for determining the ports that the container listens on.
     *
     * @param PortStrategy $strategy The port strategy to use.
     * @return self
     */
    public function withPortStrategy($strategy);

    /**
     * Set the wait strategy used for waiting for the container to start.
     *
     * @param WaitStrategy $waitStrategy The wait strategy to use.
     * @return self
     */
    public function withWaitStrategy($waitStrategy);

    /**
     * Starts the container.
     *
     * This method initializes and starts the container, returning an instance of `ContainerInstance`.
     *
     * @return ContainerInstance
     */
    public function start();
}
