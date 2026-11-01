<?php

namespace Testcontainers;

/**
 * Environments is a class that provides the ability to get environment variables.
 *
 * @method static string|null DOCKER_HOST()                                            The hostname of the docker instance
 * @method static string|null TESTCONTAINERS_HOST_OVERRIDE()                           Override the hostname retrieved from the container instance with the specified host regardless of the docker's host
 * @method static string|null TESTCONTAINERS_SSH_FEEDFORWARDING()                      Enable SSH port forwarding to the remote host that starts the container. The value should be a string in the format `[sshUser@]sshHost[:sshPort]`
 * @method static string|null TESTCONTAINERS_SSH_FEEDFORWARDING_REMOTE_HOST_OVERRIDE() The remote host to which the SSH port forwarding should be enabled
 * @method static string|null TESTCONTAINERS_LOGGER()                                  The class name of the logger to be passed to the container
 * @method static string|null TESTCONTAINERS_LOG_LEVEL()                               The minimum log level of the logger to be passed to the container
 * @method static string|null RUNNER_DEBUG()                                           Set by GitHub Actions when debug logging is enabled
 * @method static string|null ACTIONS_STEP_DEBUG()                                     Set by GitHub Actions when step debug logging is enabled
 * @method static string|null CI_DEBUG_TRACE()                                         Set by GitLab CI when debug logging is enabled
 * @method static string|null SYSTEM_DEBUG()                                           Set by Azure Pipelines when debug logging is enabled
 * @api
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
        if ($arguments !== []) {
            throw new \InvalidArgumentException('Environment getters do not accept arguments');
        }

        $value = getenv($name);
        if (is_string($value)) {
            return $value;
        }

        return null;
    }
}
