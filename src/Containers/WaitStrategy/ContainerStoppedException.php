<?php

namespace Testcontainers\Containers\WaitStrategy;

use RuntimeException;

/**
 * Exception thrown when a container stops while waiting.
 */
class ContainerStoppedException extends RuntimeException
{
}
