<?php

namespace Testcontainers\Containers;

use Testcontainers\Containers\Types\ImagePullPolicy;
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
     */
    public function getOutput();

    /**
     * Retrieve the error output from the container.
     *
     * @return string the error output from the container
     */
    public function getErrorOutput();

    /**
     * Set data associated with the container.
     *
     * @param object $value the data to associate with the container
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
     * Checks if the container is currently running.
     *
     * @return bool true if the container is running, false otherwise
     */
    public function isRunning();

    /**
     * Stops the container if it is running.
     */
    public function stop();
}
