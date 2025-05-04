<?php

namespace Testcontainers\Hook;

trait BeforeStartHook
{
    /**
     * Hook executed before the container is started.
     */
    abstract public function beforeStart();
}
