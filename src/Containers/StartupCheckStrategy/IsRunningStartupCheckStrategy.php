<?php

namespace Testcontainers\Containers\StartupCheckStrategy;

use Testcontainers\Containers\ContainerInstance;
use Testcontainers\Docker\DockerClient;
use Testcontainers\Docker\DockerClientFactory;
use Testcontainers\Utility\WithLogger;

/**
 * StartupCheckStrategy that waits until a container is running.
 */
class IsRunningStartupCheckStrategy implements StartupCheckStrategy
{
    use WithLogger;

    /**
     * The docker client.
     *
     * @var null|DockerClient
     */
    private $client;

    /**
     * The timeout duration in seconds.
     *
     * @var int the timeout duration in seconds
     */
    private $timeout = 30;

    /**
     * The interval in microseconds to wait before retrying the check.
     *
     * @var int the interval in seconds
     */
    private $retryInterval = 0;

    /**
     * Sets the docker client.
     *
     * @param DockerClient $client the docker client
     *
     * @return self
     */
    public function withDockerClient($client)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Sets the timeout duration for waiting until the container instance is ready.
     *
     * @param int $seconds the number of seconds to wait before timing out
     *
     * @return $this the current instance for method chaining
     */
    public function withTimeoutSeconds($seconds)
    {
        $this->timeout = $seconds;

        return $this;
    }

    /**
     * Sets the interval in microseconds to wait before retrying the check.
     *
     * @param int $interval the interval in microseconds
     *
     * @return $this the current instance for method chaining
     */
    public function withRetryInterval($interval)
    {
        $this->retryInterval = $interval;

        return $this;
    }

    /**
     * Wait until the container startup is successful.
     *
     * @param ContainerInstance $instance the container instance to check
     *
     * @return bool
     */
    public function waitUntilStartupSuccessful($instance)
    {
        $now = time();

        $client = $this->client ?: DockerClientFactory::create();

        try {
            $this->logger()->debug('Waiting for container to be running...');
            while (true) {
                if (time() - $now > $this->timeout) {
                    throw new WaitingTimeoutException($this->timeout);
                }

                $output = $client->inspect($instance->getContainerId());

                switch ($output->state->status) {
                    case 'running':
                        return true;

                    case 'exited':
                    case 'dead':
                        return 0 === $output->state->exitCode;

                    default:
                        $this->logger()->debug('Container is not running yet. Current status: ' . $output->state->status);
                        break;
                }
                usleep($this->retryInterval);
            }
        } catch (\Exception $e) {
            $this->logger()->debug('Error while checking container status: ' . $e->getMessage());
            return false;
        }
    }
}
