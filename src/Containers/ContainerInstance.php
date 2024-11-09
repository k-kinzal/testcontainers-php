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
