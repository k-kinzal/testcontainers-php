<?php

namespace Testcontainers\Docker\Exception;

class NoSuchObjectException extends DockerException
{
    /**
     * Checks if the given output matches the "No such object" error message.
     *
     * @param string $output the output to check
     *
     * @return bool true if the output matches the error message, false otherwise
     */
    public static function match($output)
    {
        return strpos($output, 'Error: No such object: ') === 0;
    }
}
