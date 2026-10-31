<?php

namespace Testcontainers\Containers\GenericContainer;

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
 */
class GenericContainerInstance implements ContainerInstance
{
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
     * @var array<string, mixed> the data associated with the container
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
     */
    public function __construct($containerDef)
    {
        $this->containerDef = $containerDef;
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
     */
    public function setDockerClient($client)
    {
        $this->client = $client;
    }

    public function getContainerId()
    {
        return $this->containerDef['containerId'];
    }

    public function getLabel($label)
    {
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
        if ($override) {
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

    public function getMappedPort($exposedPort)
    {
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

    public function setData($value)
    {
        $this->data[get_class($value)] = $value;
    }

    public function getData($class)
    {
        $value = $this->data[$class];
        if ($value === null) {
            throw new \LogicException("No data of type {$class} associated with the container");
        }

        return $value;
    }

    public function tryGetData($class)
    {
        if (isset($this->data[$class])) {
            return $this->data[$class];
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
                        'container='.$this->containerDef['containerId'],
                        'event='.EventAction::$CREATE,
                        'event='.EventAction::$START,
                        'event='.EventAction::$RESTART,
                        'event='.EventAction::$PAUSE,
                        'event='.EventAction::$UNPAUSE,
                        'event='.EventAction::$DIE,
                        'event='.EventAction::$DESTROY,
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
