<?php

namespace Testcontainers\Containers\GenericContainer;

use LogicException;
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
     *             labels?: array<string, string>|null,
     *             ports?: array<int, int>,
     *             pull?: ImagePullPolicy|null,
     *             privileged?: bool,
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
     *     labels?: array<string, string>|null,
     *     ports?: array<int, int>,
     *     pull?: ImagePullPolicy|null,
     *     privileged?: bool,
     * } $containerDef The container definition
     */
    public function __construct($containerDef)
    {
        $this->containerDef = $containerDef;
    }

    public function __destruct()
    {
        $this->stop();
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

    /**
     * {@inheritdoc}
     */
    public function getContainerId()
    {
        return $this->containerDef['containerId'];
    }

    /**
     * {@inheritdoc}
     */
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

    /**
     * {@inheritdoc}
     */
    public function getLabels()
    {
        if (!isset($this->containerDef['labels'])) {
            return [];
        }

        return $this->containerDef['labels'];
    }

    /**
     * {@inheritdoc}
     */
    public function getHost()
    {
        if (null !== $this->tryGetData(Session::class)) {
            return 'localhost';
        }

        $override = Environments::TESTCONTAINERS_HOST_OVERRIDE();
        if ($override) {
            return $override;
        }

        $client = $this->client ?: DockerClientFactory::create();
        $host = $client->getHost();
        if (0 === strpos($host, 'unix:///')) {
            $host = str_replace('unix:///', 'unix://', $host);
        }
        $url = parse_url($host);
        if (!isset($url['scheme'])) {
            throw new LogicException("Invalid URL: {$host}");
        }
        if (!isset($url['host'])) {
            throw new LogicException("Invalid URL: {$host}");
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

    /**
     * {@inheritdoc}
     */
    public function getExposedPorts()
    {
        if (!isset($this->containerDef['ports'])) {
            return [];
        }

        return array_keys($this->containerDef['ports']);
    }

    /**
     * {@inheritdoc}
     */
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

    /**
     * {@inheritdoc}
     */
    public function getImagePullPolicy()
    {
        return isset($this->containerDef['pull']) ? $this->containerDef['pull'] : null;
    }

    /**
     * {@inheritdoc}
     */
    public function getPrivilegedMode()
    {
        return isset($this->containerDef['privileged']) ? $this->containerDef['privileged'] : false;
    }

    /**
     * {@inheritdoc}
     */
    public function getOutput()
    {
        $client = $this->client ?: DockerClientFactory::create();
        $output = $client->logs($this->containerDef['containerId']);

        return $output->getOutput();
    }

    /**
     * {@inheritdoc}
     */
    public function getErrorOutput()
    {
        $client = $this->client ?: DockerClientFactory::create();
        $output = $client->logs($this->containerDef['containerId']);

        return $output->getErrorOutput();
    }

    /**
     * {@inheritdoc}
     */
    public function setData($value)
    {
        $this->data[get_class($value)] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function getData($class)
    {
        $value = $this->data[$class];
        if (null === $value) {
            throw new LogicException("No data of type {$class} associated with the container");
        }

        return $value;
    }

    /**
     * {@inheritdoc}
     */
    public function tryGetData($class)
    {
        if (isset($this->data[$class])) {
            return $this->data[$class];
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function isRunning()
    {
        if (false === $this->running) {
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

    /**
     * {@inheritdoc}
     */
    public function stop()
    {
        try {
            $client = $this->client ?: DockerClientFactory::create();
            $client->stop($this->containerDef['containerId']);
        } catch (NoSuchContainerException $e) {
            // Do nothing
        }
        $this->running = false;
    }
}
