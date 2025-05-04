<?php

namespace Testcontainers;

/**
 * Environments is a class that provides the ability to get environment variables.
 *
 * @method static string|null DOCKER_HOST() The hostname of the docker instance
 * @method static string|null TESTCONTAINERS_HOST_OVERRIDE() Override the hostname retrieved from the container instance with the specified host regardless of the docker's host
 * @method static string|null TESTCONTAINERS_SSH_FEEDFORWARDING() Enable SSH port forwarding to the remote host that starts the container. The value should be a string in the format `[sshUser@]sshHost[:sshPort]`
 * @method static string|null TESTCONTAINERS_SSH_FEEDFORWARDING_REMOTE_HOST_OVERRIDE() The remote host to which the SSH port forwarding should be enabled
 */
class Environments
{
    /**
     * @param string $name
     * @param array $arguments
     * @return string|null
     */
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
