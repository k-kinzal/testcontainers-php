<?php

namespace Testcontainers\Hook;

use Testcontainers\Containers\ContainerInstance;

trait AfterStartHook
{
    /**
     * Hook executed after the container is started
     *
     * @param ContainerInstance $instance
     */
    abstract public function afterStart($instance);
}
