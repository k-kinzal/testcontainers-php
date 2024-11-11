<?php

namespace Testcontainers\Containers;

use LogicException;
use RuntimeException;
use Testcontainers\Containers\PortStrategy\AlreadyExistsPortStrategyException;
use Testcontainers\Containers\PortStrategy\LocalRandomPortStrategy;
use Testcontainers\Containers\PortStrategy\PortStrategy;
use Testcontainers\Containers\PortStrategy\PortStrategyProvider;
use Testcontainers\Containers\WaitStrategy\AlreadyExistsWaitStrategyException;
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
     * @var null|string|string[]
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
     * Define the default privileged mode to be used for the container.
     *
     * @var bool|null
     */
    protected static $PRIVILEGED;

    /**
     * The privileged mode to be used for the container.
     *
     * @var bool
     */
    private $privileged = false;

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
     * The port strategy provider.
     * @var PortStrategyProvider
     */
    private $portStrategyProvider;

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
     * The wait strategy provider.
     * @var WaitStrategyProvider
     */
    private $waitStrategyProvider;

    /**
     * @param string|null $image The image to be used for the container.
     *
     * @throws AlreadyExistsPortStrategyException if the port strategy already exists.
     * @throws AlreadyExistsWaitStrategyException if the wait strategy already exists.
     */
    public function __construct($image = null)
    {
        assert($image || static::$IMAGE);

        $this->image = $image ?: static::$IMAGE;


        $this->portStrategyProvider = new PortStrategyProvider();
        $this->portStrategyProvider->register(new LocalRandomPortStrategy());
        $this->registerPortStrategy($this->portStrategyProvider);

        $this->waitStrategyProvider = new WaitStrategyProvider();
        $this->waitStrategyProvider->register(new HostPortWaitStrategy());
        $this->waitStrategyProvider->register(new HttpWaitStrategy());
        $this->waitStrategyProvider->register(new LogMessageWaitStrategy());
        $this->registerWaitStrategy($this->waitStrategyProvider);
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
        if (is_int($ports)) {
            $ports = [$ports];
        }
        if (is_string($ports)) {
            $ports = [intval($ports)];
        }
        $this->ports = $ports;

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
        $this->env[$key] = $value;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function withEnvs($env)
    {
        $this->env = array_merge($this->env, $env);

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
        $this->privileged = $mode;

        return $this;
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
     * Retrieve the ports to be exposed by the container.
     *
     * This method returns the ports that should be exposed by the container.
     * If specific ports are set, it will return those. Otherwise, it will
     * attempt to retrieve the default ports from the provider.
     *
     * @return int[]|null The ports to be exposed, or null if none are set.
     */
    protected function ports()
    {
        if (static::$PORTS) {
            return static::$PORTS;
        }
        if ($this->ports) {
            return $this->ports;
        }
        return null;
    }

    /**
     * Retrieve the environment variables for the container.
     *
     * This method returns the environment variables that should be used for the container.
     * If specific environment variables are set, it will return those. Otherwise, it will
     * attempt to retrieve the default environment variables from the provider.
     *
     * @return array|null The environment variables to be used, or null if none are set.
     */
    protected function env()
    {
        if (static::$ENVIRONMENTS) {
            return static::$ENVIRONMENTS;
        }
        if ($this->env) {
            return $this->env;
        }
        return null;
    }

    /**
     * Retrieve the privileged mode for the container.
     *
     * This method returns whether the container should run in privileged mode.
     * If a specific privileged mode is set, it will return that. Otherwise, it will
     * attempt to retrieve the default privileged mode from the provider.
     *
     * @return bool True if the container should run in privileged mode, false otherwise.
     */
    protected function privileged()
    {
        if (static::$PRIVILEGED) {
            return static::$PRIVILEGED;
        }
        return $this->privileged;
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

        $portStrategy = $this->portStrategy();
        $containerPorts = $this->ports();
        $ports = [];
        if ($portStrategy && $containerPorts) {
            foreach ($containerPorts as $containerPort) {
                $hostPort = $portStrategy->getPort();
                $ports[] = $hostPort . ':' . $containerPort;
            }
        }

        $client = DockerClientFactory::create();
        try {
            $output = $client->run($this->image, $command, $args, [
                'detach' => true,
                'env' => $this->env(),
                'publish' => $ports,
                'privileged' => $this->privileged(),
            ]);
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
        if ($output->getExitCode() !== 0) {
            throw new RuntimeException('Failed to start container');
        }
        $containerId = $output->getContainerId();
        $containerDef = [
            'image' => $this->image,
            'command' => $command,
            'args' => $args,
            'ports' => array_reduce($ports, function ($carry, $item) {
                $parts = explode(':', $item);
                $carry[(int)$parts[1]] = (int)$parts[0];
                return $carry;
            }, []),
            'env' => $this->env(),
            'privileged' => $this->privileged(),
        ];
        $instance = new GenericContainerInstance($containerId, $containerDef);

        $waitStrategy = $this->waitStrategy($instance);
        if ($waitStrategy) {
            $waitStrategy->waitUntilReady($instance);
        }

        return $instance;
    }
}
