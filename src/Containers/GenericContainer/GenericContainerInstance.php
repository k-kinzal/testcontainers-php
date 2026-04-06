<?php

namespace Testcontainers\Containers\GenericContainer;

use Testcontainers\Containers\ContainerInstance;
use Testcontainers\Containers\Types\ImagePullPolicy;
use Testcontainers\Docker\DockerClient;
use Testcontainers\Docker\DockerClientFactory;
use Testcontainers\Docker\Exception\NoSuchContainerException;
use Testcontainers\Docker\Exception\NoSuchObjectException;
use Testcontainers\Docker\Types\ContainerId;
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
     *
     * @return void
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

    /**
     * @return void
     */
    public function setData($value)
    {
        $this->data[get_class($value)] = $value;
    }

    /**
     * @template T
     * @param class-string<T> $class
     * @return T
     */
    public function getData($class)
    {
        $value = $this->tryGetData($class);
        if ($value === null) {
            throw new \LogicException("No data of type {$class} associated with the container");
        }

        return $value;
    }

    /**
     * @template T
     * @param class-string<T> $class
     * @return null|T
     */
    public function tryGetData($class)
    {
        $value = $this->data[$class] ?? null;
        if ($value === null) {
            return null;
        }
        if (!$value instanceof $class) {
            throw new \LogicException("No data of type {$class} associated with the container");
        }

        return $value;
    }

    public function isRunning()
    {
        if ($this->running === false) {
            return false;
        }

        try {
            $client = $this->client ?: DockerClientFactory::create();
            $output = $client->inspect($this->containerDef['containerId']);

            switch ($output->state->status) {
                case 'running':
                    $this->running = true;

                    return true;

                default:
                    $this->running = false;

                    return false;
            }
        } catch (NoSuchObjectException $e) {
            $this->running = false;

            return false;
        }
    }

    public function getStopTimeout()
    {
        return isset($this->containerDef['stopTimeout']) ? $this->containerDef['stopTimeout'] : null;
    }

    public function getStopSignal()
    {
        return isset($this->containerDef['stopSignal']) ? $this->containerDef['stopSignal'] : null;
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
    }
}
