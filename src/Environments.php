<?php

namespace Testcontainers;

use function Testcontainers\ensure;

/**
 * Environments is a class that provides the ability to get environment variables.
 *
 * @method static string|null DOCKER_HOST()                                            The hostname of the docker instance
 * @method static string|null TESTCONTAINERS_HOST_OVERRIDE()                           Override the hostname retrieved from the container instance with the specified host regardless of the docker's host
 * @method static string|null TESTCONTAINERS_SSH_FEEDFORWARDING()                      Enable SSH port forwarding to the remote host that starts the container. The value should be a string in the format `[sshUser@]sshHost[:sshPort]`
 * @method static string|null TESTCONTAINERS_SSH_FEEDFORWARDING_REMOTE_HOST_OVERRIDE() The remote host to which the SSH port forwarding should be enabled
 */
class Environments
{
    /**
     * @param string $name
     * @param array  $arguments
     *
     * @return null|string
     */
    public static function __callStatic($name, $arguments)
    {
        /** @psalm-suppress RedundantConditionGivenDocblockType */
        ensure(is_string($name), '$name must be string');
        /** @psalm-suppress RedundantConditionGivenDocblockType */
        ensure(is_array($arguments), '$arguments must be array');

        $value = getenv($name);
        if (is_string($value)) {
            return $value;
        }

        return null;
    }
}
