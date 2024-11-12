<?php

namespace Testcontainers\Containers;

use LogicException;
use Testcontainers\Docker\DockerClientFactory;
use Testcontainers\Docker\Exception\NoSuchContainerException;

/**
 * GenericContainerInstance is a generic implementation of docker container.
 */
class GenericContainerInstance implements ContainerInstance
{
    /**
     * The unique identifier for the container.
     *
     * @var string The container ID.
     */
    private $containerId;

    /**
     * The container definition.
     *
     * @var array{
     *     image?: string,
     *     command?: string,
     *     args?: string[],
     *     ports?: array<int, int>,
     *     env?: array<string, string>,
     * } The container definition.
     */
    private $containerDef;

    /**
     * Indicates whether the container is running.
     *
     * @var bool True if the container is running, false otherwise.
     */
    private $running = true;

    /**
     * The data associated with the container.
     *
     * @template T
     * @var array<class-string<T>, T> The data associated with the container.
     */
    private $data = [];

    /**
     * @param string $containerId The unique identifier for the container.
     */
    public function __construct($containerId, $containerDef = [])
    {
        $this->containerId = $containerId;
        $this->containerDef = $containerDef;
    }

    public function __destruct()
    {
        $this->stop();
    }

    /**
     * {@inheritdoc}
     */
    public function getContainerId()
    {
        return $this->containerId;
    }

    /**
     * {@inheritdoc}
     */
    public function getLabel($label)
    {
        if (!isset($this->containerDef['labels'])) {
            return null;
        }
        if (!is_array($this->containerDef['labels'])) {
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
        if (!is_array($this->containerDef['labels'])) {
            return [];
        }
        return $this->containerDef['labels'] ?: [];
    }

    /**
     * {@inheritdoc}
     */
    public function getHost()
    {
        // TODO: Support for host name resolution from remote hosts and from within containers
        return 'localhost';
    }

    /**
     * {@inheritdoc}
     */
    public function getExposedPorts()
    {
        if (!isset($this->containerDef['ports'])) {
            return [];
        }
        if (!is_array($this->containerDef['ports'])) {
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
        if (!is_array($this->containerDef['ports'])) {
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
    public function getPrivilegedMode()
    {
        return isset($this->containerDef['privileged']) ? $this->containerDef['privileged'] : false;
    }

    /**
     * {@inheritdoc}
     */
    public function getOutput()
    {
        $client = DockerClientFactory::create();
        $output = $client->logs($this->containerId);
        return $output->getOutput();
    }

    /**
     * {@inheritdoc}
     */
    public function getErrorOutput()
    {
        $client = DockerClientFactory::create();
        $output = $client->logs($this->containerId);
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
        if ($value === null) {
            throw new LogicException("No data of type $class associated with the container");
        }
        return $value;
    }

    /**
     * Checks if the container is currently running.
     *
     * @return bool
     */
    public function isRunning()
    {
        if ($this->running === false) {
            return false;
        }
        $client = DockerClientFactory::create();
        $output = $client->processStatus([
            'filter' => "id=$this->containerId",
        ]);
        $status = $output->get($this->containerId);
        if ($status === null) {
            $this->running = false;
            return false;
        }

        return true;
    }

    /**
     * Stops the container if it is running.
     *
     * @return void
     */
    public function stop()
    {
        try {
            $client = DockerClientFactory::create();
            $client->stop($this->containerId);
        } catch (NoSuchContainerException $e) {
            // Do nothing
        }
        $this->running = false;
    }
}
