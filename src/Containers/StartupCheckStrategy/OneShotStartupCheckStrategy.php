<?php

namespace Testcontainers\Containers\StartupCheckStrategy;

use Exception;
use Testcontainers\Containers\ContainerInstance;
use Testcontainers\Docker\DockerClient;
use Testcontainers\Docker\DockerClientFactory;
use Testcontainers\Logger\WithLogger;

/**
 * StartupCheckStrategy that waits until a container has exited with code 0.
 *
 * Useful for one-shot containers that run a command and exit (e.g. echo, printenv).
 * @api
 */
class OneShotStartupCheckStrategy implements StartupCheckStrategy
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
     * @var int the interval in microseconds
     */
    private $retryInterval = 100000;

    /**
     * Sets the docker client.
     *
     * @param DockerClient $client the docker client
     * @psalm-param mixed $client
     *
     * @return self
     */
    public function withDockerClient($client)
    {
        self::ensureInstanceOf($client, DockerClient::class);

        $this->client = $client;

        return $this;
    }

    /**
     * Sets the timeout duration for waiting until the container instance is ready.
     *
     * @param int $seconds the number of seconds to wait before timing out
     * @psalm-param mixed $seconds
     *
     * @return $this the current instance for method chaining
     */
    public function withTimeoutSeconds($seconds)
    {
        self::ensureIntRange($seconds, 0);

        $this->timeout = $seconds;

        return $this;
    }

    /**
     * Sets the interval in microseconds to wait before retrying the check.
     *
     * @param int $interval the interval in microseconds
     * @psalm-param mixed $interval
     *
     * @return $this the current instance for method chaining
     */
    public function withRetryInterval($interval)
    {
        self::ensureIntRange($interval, 0);

        $this->retryInterval = $interval;

        return $this;
    }

    /**
     * Wait until the container has exited successfully.
     *
     * @param ContainerInstance $instance the container instance to check
     * @psalm-param mixed $instance
     *
     * @return bool
     */
    public function waitUntilStartupSuccessful($instance)
    {
        self::ensureInstanceOf($instance, ContainerInstance::class);

        $now = time();

        $client = $this->client ?: DockerClientFactory::create();

        try {
            $this->logger()->debug('Waiting for one-shot container to exit...');
            while (true) {
                if (time() - $now > $this->timeout) {
                    throw new WaitingTimeoutException($this->timeout);
                }

                $output = $client->inspect($instance->getContainerId());

                switch ($output->state->status) {
                    case 'running':
                    case 'created':
                        $this->logger()->debug('Container is still running. Waiting for exit...');

                        break;

                    case 'exited':
                    case 'dead':
                        return $output->state->exitCode === 0;

                    default:
                        $this->logger()->debug('Container status: '.$output->state->status);

                        break;
                }
                usleep($this->retryInterval);
            }
        } catch (WaitingTimeoutException $e) {
            throw $e;
        } catch (Exception $e) {
            $this->logger()->debug('Error while checking container status: '.$e->getMessage());

            return false;
        }
    }
}
