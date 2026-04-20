<?php

namespace Testcontainers\Containers;

use Testcontainers\Containers\Types\ImagePullPolicy;
use Testcontainers\Docker\Exception\DockerException;
use Testcontainers\Docker\Exception\InvalidValueException;
use Testcontainers\Docker\Exception\NoSuchContainerException;
use Testcontainers\Docker\Types\ContainerId;

/**
 * An interface representing an instance of a container.
 */
interface ContainerInstance
{
    /**
     * Get the unique identifier for the container.
     *
     * @return ContainerId the container ID
     */
    public function getContainerId();

    /**
     * Get the label of the container.
     *
     * @param string $label
     *
     * @return null|string
     */
    public function getLabel($label);

    /**
     * Get the labels of the container.
     *
     * @return array<string, string>
     */
    public function getLabels();

    /**
     * Get the host address of the container.
     *
     * @return string the host address of the container
     */
    public function getHost();

    /**
     * Get the exposed ports of the container.
     *
     * @return int[] the exposed ports of the container
     */
    public function getExposedPorts();

    /**
     * Get the host port mapped to the specified exposed port.
     *
     * @param int $exposedPort the port exposed by the container
     *
     * @return null|int the host port mapped to the specified exposed port, or null if no mapping exists
     */
    public function getMappedPort($exposedPort);

    /**
     * Get the image pull policy of the container.
     *
     * @return null|ImagePullPolicy the image pull policy, or null if not set
     */
    public function getImagePullPolicy();

    /**
     * Get the privileged mode status of the container.
     *
     * @return bool true if the container is running in privileged mode, false otherwise
     */
    public function getPrivilegedMode();

    /**
     * Retrieve the standard output from the container.
     *
     * @return string the standard output from the container
     *
     * @throws NoSuchContainerException if the container no longer exists
     * @throws DockerException          if the Docker command fails
     */
    public function getOutput();

    /**
     * Retrieve the error output from the container.
     *
     * @return string the error output from the container
     *
     * @throws NoSuchContainerException if the container no longer exists
     * @throws DockerException          if the Docker command fails
     */
    public function getErrorOutput();

    /**
     * Set data associated with the container.
     *
     * @param object $value the data to associate with the container
     *
     * @return void
     */
    public function setData($value);

    /**
     * Retrieve data associated with the container.
     *
     * @template T
     *
     * @param class-string<T> $class the class name of the data to retrieve
     *
     * @return T the data associated with the specified class
     */
    public function getData($class);

    /**
     * Retrieve data associated with the container, if it exists.
     *
     * @template T
     *
     * @param class-string<T> $class the class name of the data to retrieve
     *
     * @return null|T the data associated with the specified class
     */
    public function tryGetData($class);

    /**
     * Get the stop timeout of the container.
     *
     * @return null|int the stop timeout in seconds, or null if using Docker default
     */
    public function getStopTimeout();

    /**
     * Get the stop signal of the container.
     *
     * @return null|string the stop signal name, or null if using Docker default (SIGTERM)
     */
    public function getStopSignal();

    /**
     * Checks if the container is currently running.
     *
     * @return bool true if the container is running, false otherwise
     *
     * @throws DockerException       if the Docker command fails
     * @throws InvalidValueException if the container inspect output could not be parsed
     */
    public function isRunning();

    /**
     * Stops the container if it is running.
     *
     * @return void
     *
     * @throws DockerException if the Docker command fails
     */
    public function stop();
}
