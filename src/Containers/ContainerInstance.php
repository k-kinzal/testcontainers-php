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
}
