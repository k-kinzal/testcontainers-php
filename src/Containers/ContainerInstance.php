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
}
