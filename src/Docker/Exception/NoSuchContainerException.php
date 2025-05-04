<?php

namespace Testcontainers\Docker\Exception;

/**
 * Exception thrown when a specified Docker container does not exist.
 *
 * This exception is used to indicate that an operation attempted to interact
 * with a Docker container that could not be found. It extends the DockerException
 * class to provide additional context specific to missing containers.
 */
class NoSuchContainerException extends DockerException
{
    /**
     * Checks if the given output matches the "No such container" error message.
     *
     * @param string $output the output to check
     *
     * @return bool true if the output matches the error message, false otherwise
     */
    public static function match($output)
    {
        return 0 === strpos($output, 'Error response from daemon: No such container: ');
    }
}
