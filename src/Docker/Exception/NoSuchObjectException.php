<?php

namespace Testcontainers\Docker\Exception;

use function Testcontainers\ensure;

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
        ensure(is_string($output), '$output must be string');

        return stripos($output, 'error: no such object: ') === 0;
    }
}
