<?php

namespace Testcontainers;

/**
 * Environments is a class that provides the ability to get environment variables.
 *
 * @method static string|null DOCKER_HOST() The hostname of the docker instance
 * @method static string|null TESTCONTAINERS_HOST_OVERRIDE() Override the hostname retrieved from the container instance with the specified host regardless of the docker's host
 */
class Environments
{
    private function __construct()
    {
    }

    public static function __callStatic($name, $arguments)
    {
        $value = getenv($name);
        if (is_string($value)) {
            return $value;
        } else {
            return null;
        }
    }
}
