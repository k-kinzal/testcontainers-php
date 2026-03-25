<?php

namespace Testcontainers\Docker\Exception;

/**
 * Exception thrown when a port conflict occurs during container startup.
 *
 * This is the base exception for port-related conflicts. Docker may report
 * these as either "port is already allocated" or "address already in use"
 * depending on the environment.
 */
class PortConflictException extends DockerException
{
}
