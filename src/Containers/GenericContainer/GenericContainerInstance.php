<?php

namespace Testcontainers\Containers\GenericContainer;

use Testcontainers\Utility\Ensure;
use Testcontainers\Containers\ContainerInstance;
use Testcontainers\Containers\Types\ImagePullPolicy;
use Testcontainers\Docker\DockerClient;
use Testcontainers\Docker\DockerClientFactory;
use Testcontainers\Docker\Exception\NoSuchContainerException;
use Testcontainers\Docker\Exception\NoSuchObjectException;
use Testcontainers\Docker\Types\ContainerId;
use Testcontainers\Docker\Types\Event;
use Testcontainers\Docker\Types\EventAction;
use Testcontainers\Docker\Types\State;
use Testcontainers\Environments;
use Testcontainers\SSH\Session;

/**
 * GenericContainerInstance is a generic implementation of docker container.
 * @api
 */
class GenericContainerInstance implements ContainerInstance
{
    use Ensure;

    /**
     * The docker client.
     *
     * @var null|DockerClient the docker client
     */
    private $client;

    /**
     * The container definition.
     *
     * @var array{
     *             containerId: ContainerId,
     *             labels?: null|array<string, string>,
     *             ports?: array<int, int>,
     *             pull?: null|ImagePullPolicy,
     *             privileged?: bool,
     *             stopTimeout?: null|int,
     *             stopSignal?: null|string,
     *             inspectInterval?: null|float|int,
     *             } The container definition
     */
    private $containerDef;

    /**
     * Indicates whether the container is running.
     *
     * @var bool true if the container is running, false otherwise
     */
    private $running = true;

    /**
     * Indicates whether the events of the container have been subscribed.
     *
     * @var bool
     */
    private $subscribed = false;

    /**
     * The events of the container.
     *
     * @var null|\Generator<null|Event>
     */
    private $events;

    /**
     * Indicates whether an event of the container has been observed.
     *
     * @var bool
     */
    private $eventObserved = false;

    /**
     * The state of the container from the last `docker inspect`.
     *
     * @var null|State
     */
    private $state;

    /**
     * The time of the last `docker inspect`.
     *
     * @var null|float
     */
    private $inspectedAt;

    /**
     * The data associated with the container.
     *
     * @var array<class-string, object> the data associated with the container
     */
    private $data = [];

    /**
     * @param array{
     *     containerId: ContainerId,
     *     labels?: null|array<string, string>,
     *     ports?: array<int, int>,
     *     pull?: null|ImagePullPolicy,
     *     privileged?: bool,
     *     stopTimeout?: null|int,
     *     stopSignal?: null|string,
     *     inspectInterval?: null|float|int,
     * } $containerDef The container definition
     * @psalm-param mixed $containerDef
     */
    public function __construct($containerDef)
    {
        self::ensureArray($containerDef);

        if (!isset($containerDef['containerId'])) {
            throw new \InvalidArgumentException('containerDef requires containerId');
        }
        self::ensureInstanceOf($containerDef['containerId'], ContainerId::class);

        $ports = [];
        if (isset($containerDef['ports'])) {
            $portMap = self::ensureArray($containerDef['ports']);

            foreach ($portMap as $exposed => $mapped) {
                $ports[self::ensureIntRange($exposed, 1, 65535)] = self::ensureIntRange($mapped, 1, 65535);

            }
        }

        $this->containerDef = [
            'containerId' => $containerDef['containerId'],
            'labels' => isset($containerDef['labels']) ? self::ensureStringMap($containerDef['labels']) : [],
            'ports' => $ports,
            'pull' => isset($containerDef['pull']) ? self::ensureInstanceOf($containerDef['pull'], ImagePullPolicy::class) : null,
            'privileged' => isset($containerDef['privileged']) ? self::ensureBool($containerDef['privileged']) : false,
            'stopTimeout' => isset($containerDef['stopTimeout']) ? self::ensureIntRange($containerDef['stopTimeout'], -1) : null,
            'stopSignal' => isset($containerDef['stopSignal']) ? self::ensureString($containerDef['stopSignal']) : null,
            'inspectInterval' => isset($containerDef['inspectInterval']) ? self::ensureNonNegativeNumber($containerDef['inspectInterval']) : null,
        ];

    }

    public function __destruct()
    {
        if ($this->running) {
            try {
                $this->stop();
            } catch (\Exception $e) {
                // Destructors must not throw
            }
        }
    }

    /**
     * Sets the docker client.
     *
     * @param DockerClient $client
     * @psalm-param mixed $client
     * @return void
     */
    public function setDockerClient($client)
    {
        self::ensureInstanceOf($client, DockerClient::class);

        $this->client = $client;
    }

    public function getContainerId()
    {
        return $this->containerDef['containerId'];
    }

    /**
     * @psalm-param mixed $label
     */
    public function getLabel($label)
    {
        self::ensureString($label);

        if (!isset($this->containerDef['labels'])) {
            return null;
        }
        if (!isset($this->containerDef['labels'][$label])) {
            return null;
        }

        return $this->containerDef['labels'][$label];
    }

    public function getLabels()
    {
        if (!isset($this->containerDef['labels'])) {
            return [];
        }

        return $this->containerDef['labels'];
    }

    public function getHost()
    {
        if ($this->tryGetData(Session::class) !== null) {
            return 'localhost';
        }

        $override = Environments::TESTCONTAINERS_HOST_OVERRIDE();
        if ($override !== null && $override !== '') {
            return $override;
        }

        $client = $this->client ?: DockerClientFactory::create();
        $host = $client->getHost();
        if (strpos($host, 'unix:///') === 0) {
            $host = str_replace('unix:///', 'unix://', $host);
        }
        $url = parse_url($host);
        if (!isset($url['scheme'])) {
            throw new \LogicException("Invalid URL: {$host}");
        }
        if (!isset($url['host'])) {
            throw new \LogicException("Invalid URL: {$host}");
        }

        switch ($url['scheme']) {
            case 'http':
            case 'https':
            case 'tcp':
                return $url['host'];

            default:
                return 'localhost';
        }
    }

    public function getExposedPorts()
    {
        if (!isset($this->containerDef['ports'])) {
            return [];
        }

        return array_keys($this->containerDef['ports']);
    }

    /**
     * @psalm-param mixed $exposedPort
     */
    public function getMappedPort($exposedPort)
    {
        self::ensureIntOrDigitString($exposedPort);

        if ($exposedPort < 1 || $exposedPort > 65535) {
            throw new \InvalidArgumentException('Port must be between 1 and 65535');
        }
        $exposedPort = (int) $exposedPort;

        if (!isset($this->containerDef['ports'])) {
            return null;
        }
        if (!isset($this->containerDef['ports'][$exposedPort])) {
            return null;
        }

        return $this->containerDef['ports'][$exposedPort];
    }

    public function getImagePullPolicy()
    {
        return isset($this->containerDef['pull']) ? $this->containerDef['pull'] : null;
    }

    public function getPrivilegedMode()
    {
        return isset($this->containerDef['privileged']) ? $this->containerDef['privileged'] : false;
    }

    public function getOutput()
    {
        $client = $this->client ?: DockerClientFactory::create();
        $output = $client->logs($this->containerDef['containerId']);

        return $output->getOutput();
    }

    public function getErrorOutput()
    {
        $client = $this->client ?: DockerClientFactory::create();
        $output = $client->logs($this->containerDef['containerId']);

        return $output->getErrorOutput();
    }

    /**
     * @psalm-param mixed $value
     * @return void
     */
    public function setData($value)
    {
        if (!is_object($value)) {
            throw new \InvalidArgumentException('value must be an object');
        }

        $this->data[get_class($value)] = $value;
    }

    /**
     * @template T of object
     * @param class-string<T> $class
     * @return T
     */
    public function getData($class)
    {
        self::ensureClassString($class);

        $value = $this->tryGetData($class);
        if ($value === null) {
            throw new \LogicException("No data of type {$class} associated with the container");
        }

        return $value;
    }

    /**
     * @template T of object
     * @param class-string<T> $class
     * @return null|T
     */
    public function tryGetData($class)
    {
        self::ensureClassString($class);

        if (isset($this->data[$class])) {
            return self::ensureInstanceOf($this->data[$class], $class);
        }

        return null;
    }

    /**
     * Get the state of the container.
     *
     * The state from the last `docker inspect` is reused until a new event of the container is emitted
     * or the inspect interval elapses.
     *
     * @return null|State the state of the container, or null if the container does not exist
     */
    public function getState()
    {
        if (!$this->subscribed) {
            $this->subscribed = true;

            try {
                $client = $this->client ?: DockerClientFactory::create();
                $output = $client->events([
                    'filter' => [
                        'type=container',
                        'container='.(string) $this->containerDef['containerId'],
                        'event='.EventAction::CREATE()->toString(),
                        'event='.EventAction::START()->toString(),
                        'event='.EventAction::RESTART()->toString(),
                        'event='.EventAction::PAUSE()->toString(),
                        'event='.EventAction::UNPAUSE()->toString(),
                        'event='.EventAction::DIE_()->toString(),
                        'event='.EventAction::DESTROY()->toString(),
                    ],
                    'since' => '0',
                    'timeout' => null,
                ]);
                $this->events = $output->getIterator();
            } catch (\Exception $e) {
                $this->events = null;
            }
        }

        $changed = false;
        if ($this->events !== null) {
            try {
                if ($this->events->current() === null) {
                    $this->events->next();
                }
                while ($this->events->current() !== null) {
                    $this->eventObserved = true;
                    $changed = true;
                    $this->events->next();
                }
                if (!$this->events->valid()) {
                    $this->events = null;
                }
            } catch (\Exception $e) {
                $this->events = null;
            }
        }

        $expired = $this->inspectedAt === null || microtime(true) - $this->inspectedAt >= $this->getInspectInterval();
        if ($this->events !== null && $this->eventObserved && !$changed && !$expired) {
            return $this->state;
        }

        try {
            $client = $this->client ?: DockerClientFactory::create();
            $this->state = $client->inspect($this->containerDef['containerId'])->state;
        } catch (NoSuchObjectException $e) {
            $this->state = null;
        }
        $this->inspectedAt = microtime(true);

        return $this->state;
    }

    public function isRunning()
    {
        if ($this->running === false) {
            return false;
        }

        $state = $this->getState();
        $this->running = $state !== null && $state->status === 'running';

        return $this->running;
    }

    public function isStopped()
    {
        $state = $this->getState();

        return $state === null || in_array($state->status, ['removing', 'exited', 'dead'], true);
    }

    public function getStopTimeout()
    {
        return isset($this->containerDef['stopTimeout']) ? $this->containerDef['stopTimeout'] : null;
    }

    public function getStopSignal()
    {
        return isset($this->containerDef['stopSignal']) ? $this->containerDef['stopSignal'] : null;
    }

    public function getInspectInterval()
    {
        return isset($this->containerDef['inspectInterval']) ? $this->containerDef['inspectInterval'] : 10;
    }

    /**
     * @return void
     */
    public function stop()
    {
        try {
            $client = $this->client ?: DockerClientFactory::create();
            $options = [];
            if (isset($this->containerDef['stopTimeout'])) {
                $options['timeout'] = $this->containerDef['stopTimeout'];
            }
            if (isset($this->containerDef['stopSignal'])) {
                $options['signal'] = $this->containerDef['stopSignal'];
            }
            $client->stop($this->containerDef['containerId'], $options);
        } catch (NoSuchContainerException $e) {
            // Container already gone -- expected
        }
        $this->running = false;
        $this->subscribed = true;
        $this->events = null;
    }
}
