<?php

namespace Testcontainers\Containers;

/**
 * An interface representing an instance of a container.
 */
interface ContainerInstance
{
    /**
     * Get the unique identifier for the container.
     *
     * @return string The container ID.
     */
    public function getContainerId();

    /**
     * Get the label of the container.
     *
     * @param string $label
     * @return string|null
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
     * @return string The host address of the container.
     */
    public function getHost();

    /**
     * Get the exposed ports of the container.
     *
     * @return int[] The exposed ports of the container.
     */
    public function getExposedPorts();

    /**
     * Get the host port mapped to the specified exposed port.
     *
     * @param int $exposedPort The port exposed by the container.
     * @return int|null The host port mapped to the specified exposed port, or null if no mapping exists.
     */
    public function getMappedPort($exposedPort);

    /**
     * Get the image pull policy of the container.
     *
     * @return ImagePullPolicy|null The image pull policy, or null if not set.
     */
    public function getImagePullPolicy();

    /**
     * Get the privileged mode status of the container.
     *
     * @return bool True if the container is running in privileged mode, false otherwise.
     */
    public function getPrivilegedMode();

    /**
     * Retrieve the standard output from the container.
     *
     * @return string The standard output from the container.
     */
    public function getOutput();

    /**
     * Retrieve the error output from the container.
     *
     * @return string The error output from the container.
     */
    public function getErrorOutput();


    /**
     * Set data associated with the container.
     *
     * @param object $value The data to associate with the container.
     */
    public function setData($value);

    /**
     * Retrieve data associated with the container.
     *
     * @template T
     * @param class-string<T> $class The class name of the data to retrieve.
     * @return T The data associated with the specified class.
     */
    public function getData($class);
}
