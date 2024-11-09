<?php

namespace Testcontainers\Containers;

use LogicException;
use RuntimeException;
use Testcontainers\Docker\DockerClientFactory;
use Testcontainers\Docker\DockerRunWithDetachOutput;

/**
 * GenericContainer is a generic implementation of docker container.
 */
class GenericContainer implements Container
{
    /**
     * Define the default image to be used for the container.
     * @var string|null
     */
    protected static $IMAGE;

    /**
     * The image to be used for the container.
     * @var string
     */
    private $image;

    /**
     * The commands to be executed in the container.
     * @var null|string|string[]
     */
    protected static $COMMANDS;

    /**
     * The commands to be executed in the container.
     * @var string[]
     */
    private $commands = [];

    /**
     * @param string|null $image The image to be used for the container.
     */
    public function __construct($image = null)
    {
        assert($image || static::$IMAGE);

        $this->image = $image ?: static::$IMAGE;
    }

    /**
     * {@inheritdoc}
     */
    public function withFileSystemBind($hostPath, $containerPath, $mode)
    {
        // TODO: Implement withFileSystemBind() method.
    }

    /**
     * {@inheritdoc}
     */
    public function withVolumesFrom($container, $mode)
    {
        // TODO: Implement withVolumesFrom() method.
    }

    /**
     * {@inheritdoc}
     */
    public function withExposedPorts($ports)
    {
        // TODO: Implement withExposedPorts() method.
    }

    /**
     * {@inheritdoc}
     */
    public function withCopyToContainer($transferable, $containerPath)
    {
        // TODO: Implement withCopyToContainer() method.
    }

    /**
     * {@inheritdoc}
     */
    public function withEnv($key, $value)
    {
        // TODO: Implement withEnv() method.
    }

    /**
     * {@inheritdoc}
     */
    public function withEnvs($env)
    {
        // TODO: Implement withEnvs() method.
    }

    /**
     * {@inheritdoc}
     */
    public function withLabel($key, $value)
    {
        // TODO: Implement withLabel() method.
    }

    /**
     * {@inheritdoc}
     */
    public function withLabels($labels)
    {
        // TODO: Implement withLabels() method.
    }

    /**
     * {@inheritdoc}
     */
    public function withCommand($cmd)
    {
        $this->commands = [$cmd];

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function withCommands($commandParts)
    {
        $this->commands = $commandParts;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function withExtraHost($hostname, $ipAddress)
    {
        // TODO: Implement withExtraHost() method.
    }

    /**
     * {@inheritdoc}
     */
    public function withNetworkMode($networkMode)
    {
        // TODO: Implement withNetworkMode() method.
    }

    /**
     * {@inheritdoc}
     */
    public function withNetworkAliases($aliases)
    {
        // TODO: Implement withNetworkAliases() method.
    }

    /**
     * {@inheritdoc}
     */
    public function withImagePullPolicy($policy)
    {
        // TODO: Implement withImagePullPolicy() method.
    }

    /**
     * {@inheritdoc}
     */
    public function withWorkingDirectory($workDir)
    {
        // TODO: Implement withWorkingDirectory() method.
    }

    /**
     * {@inheritdoc}
     */
    public function withStartupTimeout($timeout)
    {
        // TODO: Implement withStartupTimeout() method.
    }

    /**
     * {@inheritdoc}
     */
    public function withPrivilegedMode($mode)
    {
        // TODO: Implement withPrivilegedMode() method.
    }

    /**
     * {@inheritdoc}
     */
    public function withStartupCheckStrategy($strategy)
    {
        // TODO: Implement withStartupCheckStrategy() method.
    }

    /**
     * {@inheritdoc}
     */
    public function withPortStrategy($strategy)
    {
        // TODO: Implement withPortStrategy() method.
    }

    /**
     * {@inheritdoc}
     */
    public function withWaitStrategy($waitStrategy)
    {
        // TODO: Implement withWaitStrategy() method.
    }

    /**
     * Retrieve the command to be executed in the container.
     *
     * This method returns the command that should be executed in the container.
     * If a specific command is set, it will return that. Otherwise, it will
     * attempt to retrieve the default command from the provider.
     *
     * @return string|string[]|null
     */
    protected function commands()
    {
        if (static::$COMMANDS) {
            return static::$COMMANDS;
        }
        if ($this->commands) {
            return $this->commands;
        }
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function start()
    {
        $commands = $this->commands();
        if (is_string($commands)) {
            $commands = [$commands];
        }
        $command = null;
        $args = [];
        if (is_array($commands)) {
            $command = $commands[0];
            if (count($commands) > 1) {
                $args = array_slice($commands, 1);
            }
        }

        $client = DockerClientFactory::create();
        $output = $client->run($this->image, $command, $args, [
            'detach' => true,
        ]);
        if (!($output instanceof DockerRunWithDetachOutput)) {
            throw new LogicException('Expected DockerRunWithDetachOutput');
        }
        if ($output->getExitCode() !== 0) {
            throw new RuntimeException('Failed to start container');
        }
        $containerId = $output->getContainerId();

        return new GenericContainerInstance($containerId);
    }
}
