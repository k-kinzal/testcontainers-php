<?php

namespace Testcontainers\Containers;

use Exception;
use LogicException;
use Testcontainers\Containers\PortStrategy\LocalRandomPortStrategy;
use Testcontainers\Containers\PortStrategy\PortStrategy;
use Testcontainers\Containers\PortStrategy\PortStrategyProvider;
use Testcontainers\Containers\WaitStrategy\HostPortWaitStrategy;
use Testcontainers\Containers\WaitStrategy\HttpWaitStrategy;
use Testcontainers\Containers\WaitStrategy\LogMessageWaitStrategy;
use Testcontainers\Containers\WaitStrategy\WaitStrategy;
use Testcontainers\Containers\WaitStrategy\WaitStrategyProvider;
use Testcontainers\Docker\DockerClientFactory;
use Testcontainers\Docker\DockerRunWithDetachOutput;
use Testcontainers\Docker\Exception\PortAlreadyAllocatedException;

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
     * @var null|string[]
     */
    protected static $COMMANDS;

    /**
     * The commands to be executed in the container.
     * @var string[]
     */
    private $commands = [];

    /**
     * Define the default ports to be exposed by the container.
     * @var int[]|null
     */
    protected static $PORTS;

    /**
     * The ports to be exposed by the container.
     * @var int[]
     */
    private $ports = [];

    /**
     * Define the default environment variables to be used for the container.
     * @var array|null
     */
    protected static $ENVIRONMENTS;

    /**
     * The environment variables to be used for the container.
     * @var array
     */
    private $env = [];

    /**
     * Define the default port strategy to be used for the container.
     * @var string|null
     */
    protected static $PORT_STRATEGY;

    /**
     * The port strategy to be used for the container.
     * @var PortStrategy|null
     */
    private $portStrategy;

    /**
     * Define the default wait strategy to be used for the container.
     * @var string|null
     */
    protected static $WAIT_STRATEGY;

    /**
     * The wait strategy to be used for the container.
     * @var WaitStrategy|null
     */
    private $waitStrategy;

    /**
     * The port strategy provider.
     * @var PortStrategyProvider
     */
    private $portStrategyProvider;

    /**
     * The wait strategy provider.
     * @var WaitStrategyProvider
     */
    private $waitStrategyProvider;

    /**
     * @param string|null $image The image to be used for the container.
     *
     * @throws Exception If the port strategy is not found.
     */
    public function __construct($image = null)
    {
        assert($image || static::$IMAGE);

        $this->portStrategyProvider = new PortStrategyProvider();
        $this->portStrategyProvider->register(new LocalRandomPortStrategy());
        $this->registerPortStrategy($this->portStrategyProvider);

        $this->waitStrategyProvider = new WaitStrategyProvider();
        $this->waitStrategyProvider->register(new HostPortWaitStrategy());
        $this->waitStrategyProvider->register(new HttpWaitStrategy());
        $this->waitStrategyProvider->register(new LogMessageWaitStrategy());
        $this->registerWaitStrategy($this->waitStrategyProvider);

        $this->image = $image ?: static::$IMAGE;
        if (static::$PORTS) {
            $this->ports = static::$PORTS;
        }
        if (!empty(static::$ENVIRONMENTS)) {
            $this->env = static::$ENVIRONMENTS;
        }
        if (static::$COMMANDS) {
            $this->commands = static::$COMMANDS;
        }
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
        if (is_array($ports)) {
            $this->ports = $ports;
        } else {
            $this->ports = [$ports];
        }

        return $this;
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
        $this->env[] = "$key=$value";

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function withEnvs($env)
    {
        foreach ($env as $key => $value) {
            $this->env[] = "$key=$value";
        }

        return $this;
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
        $this->portStrategy = $strategy;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function withWaitStrategy($waitStrategy)
    {
        $this->waitStrategy = $waitStrategy;

        return $this;
    }

    /**
     * Retrieve the port strategy for the container.
     *
     * This method returns the port strategy that should be used for the container.
     * If a specific port strategy is set, it will return that. Otherwise, it will
     * attempt to retrieve the default port strategy from the provider.
     *
     * @return PortStrategy|null The port strategy to be used, or null if none is set.
     */
    protected function portStrategy()
    {
        if (static::$PORT_STRATEGY !== null) {
            $strategy = $this->portStrategyProvider->get(static::$PORT_STRATEGY);
            if (!$strategy) {
                throw new LogicException("Port strategy not found: " . static::$PORT_STRATEGY);
            }
            return $strategy;
        }
        if ($this->portStrategy) {
            return $this->portStrategy;
        }
        return null;
    }

    /**
     * Retrieve the wait strategy for the container.
     *
     * This method returns the wait strategy that should be used for the container.
     * If a specific wait strategy is set, it will return that. Otherwise, it will
     * attempt to retrieve the default wait strategy from the provider.
     *
     * @param ContainerInstance $instance The container instance for which to get the wait strategy.
     * @return WaitStrategy|null The wait strategy to be used, or null if none is set.
     */
    protected function waitStrategy($instance)
    {
        if (static::$WAIT_STRATEGY !== null) {
            $strategy = $this->waitStrategyProvider->get(static::$WAIT_STRATEGY);
            if (!$strategy) {
                throw new LogicException("Wait strategy not found: " . static::$WAIT_STRATEGY);
            }
            return $strategy;
        }
        if ($this->waitStrategy) {
            return $this->waitStrategy;
        }
        return null;
    }

    /**
     * Register a port strategy.
     *
     * @param PortStrategyProvider $provider The port strategy provider.
     */
    protected function registerPortStrategy($provider)
    {
        // Override this method to register custom port strategies
    }

    /**
     * Register a wait strategy.
     *
     * @param WaitStrategyProvider $provider The wait strategy provider.
     */
    protected function registerWaitStrategy($provider)
    {
        // Override this method to register custom wait strategies
    }

    /**
     * {@inheritdoc}
     */
    public function start()
    {
        $client = DockerClientFactory::create();
        $portStrategy = $this->portStrategy();

        $containerDef = ['image' => $this->image];

        $command = null;
        $args = null;
        if ($this->commands) {
            $commands = $this->commands;
            $command = array_shift($commands);
            $args = $commands;

            $containerDef['cmd'] = $command;
            $containerDef['args'] = $args;
        }
        $options = ['detach' => true];
        if (!empty($this->ports) && $portStrategy) {
            $containerDef['mappingPort'] = [];
            foreach ($this->ports as $port) {
                $hostPort = $portStrategy->getPort();
                $options['publish'][] = "$hostPort:$port";

                $containerDef['mappingPort'][$port] = $hostPort;
            }
        }
        if (!empty($this->env)) {
            $options['env'] = $this->env;

            $containerDef['env'] = $this->env;
        }

        try {
            $output = $client->run($this->image, $command, $args, $options);
        } catch (PortAlreadyAllocatedException $e) {
            $behavior = $portStrategy->conflictBehavior();
            if ($behavior->isRetry()) {
                return $this->start();
            }
            if ($behavior->isFail()) {
                throw $e;
            }
            throw new LogicException('Unknown conflict behavior: `' . $behavior . '`', 0, $e);
        }

        if (!($output instanceof DockerRunWithDetachOutput)) {
            throw new LogicException('Expected DockerRunWithDetachOutput');
        }
        $containerId = $output->getContainerId();

        $instance = new GenericContainerInstance($containerId, $containerDef);

        $waitStrategy = $this->waitStrategy($instance);
        if ($waitStrategy) {
            $waitStrategy->waitUntilReady($instance);
        }

        return $instance;
    }
}
