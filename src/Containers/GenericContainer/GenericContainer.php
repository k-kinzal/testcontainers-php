<?php

namespace Testcontainers\Containers\GenericContainer;

use LogicException;
use RuntimeException;
use Testcontainers\Containers\BindMode;
use Testcontainers\Containers\Container;
use Testcontainers\Containers\ContainerInstance;
use Testcontainers\Containers\ImagePullPolicy;
use Testcontainers\Containers\PortStrategy\AlreadyExistsPortStrategyException;
use Testcontainers\Containers\PortStrategy\LocalRandomPortStrategy;
use Testcontainers\Containers\PortStrategy\PortStrategy;
use Testcontainers\Containers\PortStrategy\PortStrategyProvider;
use Testcontainers\Containers\StartupCheckStrategy\AlreadyExistsStartupStrategyException;
use Testcontainers\Containers\StartupCheckStrategy\IsRunningStartupCheckStrategy;
use Testcontainers\Containers\StartupCheckStrategy\StartupCheckStrategy;
use Testcontainers\Containers\StartupCheckStrategy\StartupCheckStrategyProvider;
use Testcontainers\Containers\WaitStrategy\AlreadyExistsWaitStrategyException;
use Testcontainers\Containers\WaitStrategy\HostPortWaitStrategy;
use Testcontainers\Containers\WaitStrategy\HttpWaitStrategy;
use Testcontainers\Containers\WaitStrategy\LogMessageWaitStrategy;
use Testcontainers\Containers\WaitStrategy\WaitStrategy;
use Testcontainers\Containers\WaitStrategy\WaitStrategyProvider;
use Testcontainers\Docker\DockerClient;
use Testcontainers\Docker\DockerClientFactory;
use Testcontainers\Docker\Exception\BindAddressAlreadyUseException;
use Testcontainers\Docker\Output\DockerRunWithDetachOutput;
use Testcontainers\Docker\Exception\PortAlreadyAllocatedException;
use Testcontainers\Exceptions\InvalidFormatException;

/**
 * GenericContainer is a generic implementation of docker container.
 */
class GenericContainer implements Container
{
    use EnvSetting;
    use ExposedPortSetting;
    use GeneralSetting;
    use HostSetting;
    use MountSetting;
    use NetworkAliasSetting;
    use NetworkModeSetting;
    use VolumesFromSetting;

    /**
     * The Docker client.
     * @var DockerClient|null
     */
    private $client;

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
     * Define the default labels to be used for the container.
     * @var array<string, string>|null
     */
    protected static $LABELS;

    /**
     * The labels to be used for the container.
     * @var array<string, string>
     */
    private $labels = [];

    /**
     * Define the default image pull policy to be used for the container.
     * @var ImagePullPolicy|null
     */
    protected static $PULL_POLICY;

    /**
     * The image pull policy to be used for the container.
     * @var ImagePullPolicy|null
     */
    private $pullPolicy;

    /**
     * Define the default working directory to be used for the container.
     * @var string|null
     */
    protected static $WORKDIR;

    /**
     * The working directory to be used for the container.
     * @var string|null
     */
    private $workDir;

    /**
     * Define the default startup timeout to be used for the container.
     * @var int|null
     */
    protected static $STARTUP_TIMEOUT;

    /**
     * The startup timeout to be used for the container.
     * @var int|null
     */
    private $startupTimeout;

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
     * Define the default startup check strategy to be used for the container.
     * @var string|null
     */
    protected static $STARTUP_CHECK_STRATEGY;

    /**
     * The startup check strategy to be used for the container.
     * @var StartupCheckStrategy|null
     */
    private $startupCheckStrategy;

    /**
     * The startup check strategy provider.
     * @var StartupCheckStrategyProvider
     */
    private $startupCheckStrategyProvider;

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
     * @throws AlreadyExistsStartupStrategyException if the startup strategy already exists.
     * @throws AlreadyExistsPortStrategyException if the port strategy already exists.
     * @throws AlreadyExistsWaitStrategyException if the wait strategy already exists.
     */
    public function __construct($image = null)
    {
        assert($image || static::$IMAGE);

        $this->image = $image ?: static::$IMAGE;

        $this->startupCheckStrategyProvider = new StartupCheckStrategyProvider();
        $this->startupCheckStrategyProvider->register(new IsRunningStartupCheckStrategy());

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
     * Set the Docker client.
     * @param DockerClient $client The Docker client.
     * @return self
     */
    public function withDockerClient($client)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function withLabel($key, $value)
    {
        $this->labels[$key] = $value;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function withLabels($labels)
    {
        $this->labels = $labels;

        return $this;
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
    public function withImagePullPolicy($policy)
    {
        $this->pullPolicy = $policy;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function withWorkingDirectory($workDir)
    {
        $this->workDir = $workDir;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function withStartupTimeout($timeout)
    {
        $this->startupTimeout = $timeout;

        return $this;
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
        $this->startupCheckStrategy = $strategy;

        return $this;
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
     * Retrieve the labels for the container.
     *
     * This method returns the labels that should be used for the container.
     * If specific labels are set, it will return those. Otherwise, it will
     * attempt to retrieve the default labels from the provider.
     *
     * @return array<string, string>|null The labels to be used, or null if none are set.
     */
    protected function labels()
    {
        if (static::$LABELS) {
            return static::$LABELS;
        }
        if ($this->labels) {
            return $this->labels;
        }
        return null;
    }

    /**
     * Retrieve the image pull policy for the container.
     *
     * This method returns the image pull policy that should be used for the container.
     * If a specific image pull policy is set, it will return that. Otherwise, it will
     * attempt to retrieve the default image pull policy from the provider.
     *
     * @return ImagePullPolicy|null The image pull policy to be used, or null if none is set.
     */
    protected function pullPolicy()
    {
        if (static::$PULL_POLICY) {
            return static::$PULL_POLICY;
        }
        return $this->pullPolicy;
    }

    /**
     * Retrieve the working directory for the container.
     *
     * @return string|null
     */
    protected function workDir()
    {
        if (static::$WORKDIR) {
            return static::$WORKDIR;
        }
        return $this->workDir;
    }

    /**
     * Retrieve the startup timeout for the container.
     *
     * @return int|null
     */
    protected function startupTimeout()
    {
        if (static::$STARTUP_TIMEOUT) {
            return static::$STARTUP_TIMEOUT;
        }
        return $this->startupTimeout;
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
     * Retrieve the startup check strategy for the container.
     *
     * This method returns the startup check strategy that should be used for the container.
     * If a specific startup check strategy is set, it will return that. Otherwise, it will
     * attempt to retrieve the default startup check strategy from the provider.
     *
     * @return StartupCheckStrategy|null The startup check strategy to be used, or null if none is set.
     */
    protected function startupCheckStrategy()
    {
        if (static::$STARTUP_CHECK_STRATEGY !== null) {
            $strategy = $this->startupCheckStrategyProvider->get(static::$STARTUP_CHECK_STRATEGY);
            if (!$strategy) {
                throw new LogicException("Startup check strategy not found: " . static::$STARTUP_CHECK_STRATEGY);
            }
            return $strategy;
        }
        if ($this->startupCheckStrategy) {
            return $this->startupCheckStrategy;
        }
        return null;
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
    protected function waitStrategy(/** @noinspection PhpUnusedParameterInspection */ $instance)
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
     *
     * @throws InvalidFormatException If the provided mode is not valid.
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

        $extraHosts = $this->extraHosts();
        $hosts = [];
        if ($extraHosts) {
            foreach ($extraHosts as $host) {
                $hosts[] = $host->toString();
            }
        }

        $containerVolumes = $this->volumesFrom();
        $volumesFrom = [];
        if ($containerVolumes) {
            foreach ($containerVolumes as $volume) {
                $s = $volume['name'];
                if ($volume['mode'] === BindMode::READ_ONLY()) {
                    $s .= ':ro';
                }
                $volumesFrom[] = $s;
            }
        }

        $portStrategy = $this->portStrategy();
        $containerPorts = $this->exposedPorts();
        $ports = [];
        if ($portStrategy && $containerPorts) {
            foreach ($containerPorts as $containerPort) {
                $hostPort = $portStrategy->getPort();
                $ports[] = $hostPort . ':' . $containerPort;
            }
        }

        $client = $this->client ?: DockerClientFactory::create();

        try {
            $options = [
                'addHost' => $hosts,
                'detach' => true,
                'env' => $this->env(),
                'label' => $this->labels(),
                'mount' => $this->mounts(),
                'network' => $this->networkMode(),
                'networkAlias' => $this->networkAliases(),
                'volumesFrom' => $volumesFrom,
                'publish' => $ports,
                'pull' => $this->pullPolicy(),
                'workdir' => $this->workDir(),
                'privileged' => $this->privileged(),
                'name' => $this->name(),
            ];
            $timeout = $this->startupTimeout();
            if ($timeout !== null) {
                $output = $client->withTimeout($timeout)->run($this->image, $command, $args, $options);
            } else {
                $output = $client->run($this->image, $command, $args, $options);
            }
        } catch (PortAlreadyAllocatedException $e) {
            if ($portStrategy === null) {
                throw $e;
            }
            $behavior = $portStrategy->conflictBehavior();
            if ($behavior->isRetry()) {
                return $this->start();
            }
            if ($behavior->isFail()) {
                throw $e;
            }
            throw new LogicException('Unknown conflict behavior: `' . $behavior . '`', 0, $e);
        } catch (BindAddressAlreadyUseException $e) {
            if ($portStrategy === null) {
                throw $e;
            }
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
            'name' => $this->name(),
            'hosts' => $hosts,
            'env' => $this->env(),
            'labels' => $this->labels(),
            'mounts' => $this->mounts(),
            'networkMode' => $this->networkMode(),
            'networkAliases' => $this->networkAliases(),
            'volumesFrom' => $volumesFrom,
            'ports' => array_reduce($ports, function ($carry, $item) {
                $parts = explode(':', $item);
                $carry[(int)$parts[1]] = (int)$parts[0];
                return $carry;
            }, []),
            'pull' => $this->pullPolicy(),
            'workdir' => $this->workDir(),
            'privileged' => $this->privileged(),
        ];
        $instance = new GenericContainerInstance($containerId, $containerDef);
        $instance->setDockerClient($client);

        $startupCheckStrategy = $this->startupCheckStrategy();
        if ($startupCheckStrategy) {
            if ($startupCheckStrategy->waitUntilStartupSuccessful($instance) === false) {
                throw new RuntimeException('Illegal state of container');
            }
        }

        $waitStrategy = $this->waitStrategy($instance);
        if ($waitStrategy) {
            $waitStrategy->waitUntilReady($instance);
        }

        return $instance;
    }
}
