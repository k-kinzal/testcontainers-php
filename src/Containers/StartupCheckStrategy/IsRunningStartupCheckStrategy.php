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
     * Wait until the container startup is successful.
     *
     * @param ContainerInstance $instance the container instance to check
     *
     * @return bool
     */
    public function waitUntilStartupSuccessful($instance)
    {
        $client = $this->client ?: DockerClientFactory::create();

        try {
            $this->logger()->debug('Waiting for container to be running...');
            while (true) {
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
                usleep(0);
            }
        } catch (\Exception $e) {
            $this->logger()->debug('Error while checking container status: ' . $e->getMessage());
            return false;
        }
    }
}
