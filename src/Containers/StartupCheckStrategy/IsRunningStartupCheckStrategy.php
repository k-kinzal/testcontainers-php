<?php

namespace Testcontainers\Containers\StartupCheckStrategy;

use Exception;
use Testcontainers\Docker\DockerClient;
use Testcontainers\Docker\DockerClientFactory;

/**
 * StartupCheckStrategy that waits until a container is running.
 */
class IsRunningStartupCheckStrategy implements StartupCheckStrategy
{
    /**
     * The docker client.
     * @var DockerClient|null
     */
    private $client;

    /**
     * {@inheritdoc}
     */
    public function waitUntilStartupSuccessful($instance)
    {
        $client = $this->client ?: DockerClientFactory::create();
        try {
            while (true) {
                $output = $client->inspect($instance->getContainerId());
                switch ($output->state->status) {
                    case 'running':
                        return true;
                    case 'exited':
                    case 'dead':
                        return $output->state->exitCode === 0;
                    default:
                        break;
                }
                usleep(0);
            }
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Sets the docker client.
     * @param DockerClient $client The docker client.
     * @return self
     */
    public function withDockerClient($client)
    {
        $this->client = $client;
        return $this;
    }
}
