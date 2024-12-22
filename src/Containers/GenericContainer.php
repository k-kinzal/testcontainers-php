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
use Testcontainers\Exceptions\InvalidFormatException;

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
     * Define the default extra hosts to be used for the container.
     * @var array{
     *      hostname: string,
     *      ipAddress: string
     *  }[]|null
     */
    protected static $EXTRA_HOSTS;

    /**
     * The extra hosts to be used for the container.
     * @var array{
     *      hostname: string,
     *      ipAddress: string
     *  }[]
     */
    private $extraHosts = [];

    /**
     * Define the default mounts to be used for the container.
     * @var string[]|null
     */
    protected static $MOUNTS;

    /**
     * The mounts to be used for the container.
     * @var array{
     *     'hostPath': string,
     *     'containerPath': string,
     *     'mode': BindMode
     * }
     */
    private $mounts = [];

    /**
     * Define the default volumes to be used for the container.
     * @var string[]|null
     */
    protected static $VOLUMES_FROM;

    /**
     * The volumes to be used for the container.
     * @var array{
     *    name: string,
     *    mode: BindMode,
     * }[]
     */
    private $volumesFrom = [];

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
        $this->mounts[] = [
            'type' => 'bind',
            'hostPath' => $hostPath,
            'containerPath' => $containerPath,
            'mode' => $mode,
        ];

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function withVolumesFrom($container, $mode)
    {
        $this->volumesFrom[] = [
            'name' => $container->getContainerId(),
            'mode' => $mode,
        ];

        return $this;
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
    public function withExtraHost($hostname, $ipAddress)
    {
        $this->extraHosts[] = [
            'hostname' => $hostname,
            'ipAddress' => $ipAddress,
        ];

        return $this;
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
     * Retrieve the extra hosts to be used for the container.
     *
     * @return array{
     *     hostname: string,
     *     ipAddress: string
     * }[]
     */
    protected function extraHost()
    {
        if (static::$EXTRA_HOSTS) {
            return static::$EXTRA_HOSTS;
        }
        if ($this->extraHosts) {
            return $this->extraHosts;
        }
        return [];
    }

    /**
     * Retrieve the mounts to be used for the container.
     *
     * This method returns an array of mounts, where each mount is an associative array
     * containing the host path, container path, and bind mode.
     *
     * @return array{
     *   hostPath: string,
     *   containerPath: string,
     *   mode: BindMode
     * }[] The mounts to be used for the container.
     *
     * @throws InvalidFormatException If the mount format is invalid.
     */
    protected function mounts()
    {
        $targets = static::$MOUNTS;
        if ($targets === null) {
            $targets = $this->mounts;
        }

        $mounts = [];
        foreach ($targets as $mount) {
            if (is_array($mount)) {
                $mounts[] = $mount;
            } elseif (strpos($mount, ':') > 0) {
                // source:destination[:mode]
                $parts = explode(':', $mount);
                $mounts[] = [
                    'hostPath' => $parts[0],
                    'containerPath' => $parts[1],
                    'mode' => isset($parts[2]) ? BindMode::fromString($parts[2]) : BindMode::READ_WRITE(),
                ];
            } elseif (strpos($mount, ',') === 0) {
                // type=bind,source=...,target=...,readonly
                $parts = explode(',', $mount);
                $mount = [];
                foreach ($parts as $part) {
                    $subParts = explode('=', $part);
                    if ($subParts[0] === 'type') {
                        switch ($subParts[1]) {
                            case 'bind':
                            case 'tmpfs':
                                $mount['type'] = $subParts[1];
                                break;
                            default:
                                throw new LogicException('Invalid mount type: ' . $subParts[1]);
                        }
                    } elseif ($subParts[0] === 'source' || $subParts[0] === 'src') {
                        $mount['hostPath'] = $subParts[1];
                    } elseif ($subParts[0] === 'target' || $subParts[0] === 'destination' || $subParts[0] === 'dst') {
                        $mount['containerPath'] = $subParts[1];
                    } elseif ($subParts[0] === 'readonly') {
                        $mount['mode'] = BindMode::READ_ONLY();
                    } elseif ($subParts[0] === 'bind-propagation') {
                        switch ($subParts[1]) {
                            case 'rprivate':
                            case 'private':
                            case 'rshared':
                            case 'shared':
                            case 'rslave':
                            case 'slave':
                                $mount['propagation'] = $subParts[1];
                                break;
                            default:
                                throw new LogicException('Invalid bind propagation: ' . $subParts[1]);
                        }
                    }
                }
                if (!isset($mount['type'])) {
                    $mount['type'] = 'bind';
                }
                if (!isset($mount['mode'])) {
                    $mount['mode'] = BindMode::READ_WRITE();
                }
                if (!isset($mount['hostPath'])) {
                    throw new LogicException('Missing host path in mount');
                }
                if (!isset($mount['containerPath'])) {
                    throw new LogicException('Missing container path in mount');
                }
                $mounts[] = $mount;
            } else {
                throw new LogicException('Invalid mount format: ' . $mount);
            }
        }

        return empty($mounts) ? null : $mounts;
    }

    /**
     * Retrieve the volumes to be used for the container.
     *
     * This method returns an array of volumes, where each volume is an associative array
     * containing the container name and bind mode.
     *
     * @return array{
     *     name: string,
     *     mode: BindMode,
     * }[] The volumes to be used for the container.
     *
     * @throws InvalidFormatException If the volume format is invalid.
     */
    protected function volumesFrom()
    {
        $targets = static::$VOLUMES_FROM;
        if ($targets === null) {
            $targets = $this->volumesFrom;
        }

        $volumesFrom = [];
        foreach ($targets as $volume) {
            if (is_string($volume)) {
                $parts = explode(':', $volume);
                $volume = [
                    'name' => $parts[0],
                    'mode' => isset($parts[1]) ? BindMode::fromString($parts[1]) : BindMode::READ_WRITE(),
                ];
            }

            if (!isset($volume['name'])) {
                throw new LogicException('Missing container name in volumes from');
            }
            if (!isset($volume['mode'])) {
                throw new LogicException('Missing bind mode in volumes from');
            }

            $volumesFrom[] = $volume;
        }

        return empty($volumesFrom) ? null : $volumesFrom;
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

        $extraHosts = $this->extraHost();
        $hosts = [];
        if ($extraHosts) {
            foreach ($extraHosts as $extraHost) {
                $hosts[] = $extraHost['hostname'] . ':' . $extraHost['ipAddress'];
            }
        }

        $bindMounts = $this->mounts();
        $mounts = [];
        if ($bindMounts) {
            foreach ($bindMounts as $mount) {
                $s = sprintf('type=%s,source=%s,target=%s', $mount['type'], $mount['hostPath'], $mount['containerPath']);
                if ($mount['mode'] === BindMode::READ_ONLY()) {
                    $s .= ',readonly';
                }
                if (isset($mount['propagation'])) {
                    $s .= ',bind-propagation=' . $mount['propagation'];
                }
                $mounts[] = $s;
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
                'addHost' => $hosts,
                'detach' => true,
                'env' => $this->env(),
                'label' => $this->labels(),
                'mount' => $mounts,
                'volumesFrom' => $volumesFrom,
                'publish' => $ports,
                'pull' => $this->pullPolicy(),
                'workdir' => $this->workDir(),
                'privileged' => $this->privileged(),
            ]);
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
            'hosts' => $hosts,
            'env' => $this->env(),
            'labels' => $this->labels(),
            'mounts' => $mounts,
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

        $waitStrategy = $this->waitStrategy($instance);
        if ($waitStrategy) {
            $waitStrategy->waitUntilReady($instance);
        }

        return $instance;
    }
}
