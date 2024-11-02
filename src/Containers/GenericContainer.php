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
        // TODO: Implement withCommand() method.
    }

    /**
     * {@inheritdoc}
     */
    public function withCommands($commandParts)
    {
        // TODO: Implement withCommands() method.
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
     * {@inheritdoc}
     */
    public function start()
    {
        $client = DockerClientFactory::create();
        $output = $client->run($this->image, null, null, [
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
