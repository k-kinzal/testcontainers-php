<?php

namespace Testcontainers\Containers\WaitStrategy;

use RuntimeException;

/**
 * Exception thrown when a container stops while waiting.
 */
final class ContainerStoppedException extends RuntimeException
{
}
