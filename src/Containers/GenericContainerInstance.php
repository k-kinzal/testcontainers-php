<?php

namespace Testcontainers\Containers;

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
     * Indicates whether the container is running.
     *
     * @var bool True if the container is running, false otherwise.
     */
    private $running = true;

    /**
     * @param string $containerId The unique identifier for the container.
     */
    public function __construct($containerId)
    {
        $this->containerId = $containerId;
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
