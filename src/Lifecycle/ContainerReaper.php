<?php

namespace Testcontainers\Lifecycle;

use Testcontainers\Docker\DockerClient;
use Testcontainers\Docker\Exception\DockerException;
use Testcontainers\Docker\Exception\InvalidValueException;

/**
 * Detects and stops orphaned containers whose owning process has died.
 */
class ContainerReaper
{
    /**
     * @var DockerClient
     */
    protected $client;

    /**
     * @param DockerClient $client
     */
    public function __construct(DockerClient $client)
    {
        $this->client = $client;
    }

    /**
     * Find and stop orphaned testcontainers containers.
     *
     * Queries Docker for containers labeled with `org.testcontainers=true`,
     * checks if the owning process (from `org.testcontainers.pid` label) is still alive,
     * and stops containers whose owning process has died.
     *
     * Safe for concurrent use: containers owned by other running processes are never touched.
     */
    public function execute()
    {
        try {
            $output = $this->client->ps([
                'all' => true,
                'filter' => ['label=org.testcontainers=true'],
            ]);

            $currentPid = getmypid();
            $currentHost = (string) gethostname();
            foreach ($output->getContainers() as $container) {
                $containerHost = $container->getLabel('org.testcontainers.host');

                if ($containerHost === null || $containerHost !== $currentHost) {
                    continue;
                }

                $pid = $container->getLabel('org.testcontainers.pid');

                if ($pid !== null && (int) $pid === $currentPid) {
                    continue;
                }

                if ($pid !== null && $this->isProcessAlive((int) $pid)) {
                    continue;
                }

                try {
                    $this->client->stop($container->id);
                } catch (DockerException $e) {
                    // Container may already be stopped
                }
            }
        } catch (DockerException $e) {
            // Best-effort; don't fail the test run
        } catch (InvalidValueException $e) {
            // Best-effort; docker ps output parse failure
        }
    }

    /**
     * Check if a process with the given PID is still alive.
     *
     * @param int $pid the process ID to check
     *
     * @return bool true if the process is alive, false otherwise
     */
    protected function isProcessAlive($pid)
    {
        if (function_exists('posix_kill')) {
            return @posix_kill($pid, 0);
        }

        if (file_exists("/proc/{$pid}/status")) {
            return true;
        }

        $result = @shell_exec("kill -0 {$pid} 2>/dev/null && echo 1 || echo 0");

        return trim($result) === '1';
    }
}
