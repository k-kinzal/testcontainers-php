<?php

namespace Testcontainers\Docker\Output;

use LogicException;
use Symfony\Component\Process\Process;

/**
 * Represents the status output of a Docker process.
 *
 * This class extends DockerOutput to include additional status information
 * for Docker containers, providing methods to retrieve the status details
 * for specific containers.
 */
class DockerProcessStatusOutput extends DockerOutput
{
    /**
     * An associative array that holds the status information of Docker containers.
     *
     * The array keys are container IDs (as strings), and the values are arrays containing
     * the status details for each container.
     *
     * @var array<string, array<string, mixed>>
     */
    private $statuses;

    /**
     * @param Process $process
     * @param array<string, array<string, mixed> $statuses
     */
    public function __construct($process, $statuses)
    {
        parent::__construct($process);

        $this->statuses = [];
        foreach ($statuses as $status) {
            if (!isset($status['ID'])) {
                throw new LogicException('Status ID is required');
            }
            $this->statuses[$status['ID']] = $status;
        }
    }

    /**
     * Retrieve the status information for a specific Docker container.
     *
     * This method returns the status details for the given container ID.
     * The container ID can be either a 64-character full ID or a 12-character short ID.
     *
     * @param string $containerId The ID of the Docker container (64 or 12 characters).
     * @return array|null The status details of the Docker container, or null if not found.
     */
    public function get($containerId)
    {
        assert(strlen($containerId) === 64 || strlen($containerId) === 12);

        if (strlen($containerId) === 64) {
            $containerId = substr($containerId, 0, 12);
        }
        return isset($this->statuses[$containerId]) ? $this->statuses[$containerId] : null;
    }
}
