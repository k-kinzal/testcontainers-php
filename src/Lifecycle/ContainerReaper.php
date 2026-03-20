<?php

namespace Testcontainers\Lifecycle;

use Testcontainers\Docker\DockerClient;

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
            foreach ($output->getContainers() as $container) {
                $pid = $container->getLabel('org.testcontainers.pid');

                // Skip containers owned by the current process
                if ($pid !== null && (int) $pid === $currentPid) {
                    continue;
                }

                // Skip containers whose owning process is still alive
                if ($pid !== null && $this->isProcessAlive((int) $pid)) {
                    continue;
                }

                // Owning process is dead -- container is orphaned, stop it.
                // Removal is left to Docker's --rm flag or explicit user action.
                try {
                    $this->client->stop($container->id);
                } catch (\Exception $e) {
                    // Container may already be stopped
                }
            }
        } catch (\Exception $e) {
            // Cleanup is best-effort; don't fail the test run
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
            // Signal 0 checks process existence without actually sending a signal
            return @posix_kill($pid, 0);
        }

        // Linux: check /proc filesystem
        if (file_exists("/proc/{$pid}/status")) {
            return true;
        }

        // macOS/Unix fallback
        $result = @shell_exec("kill -0 {$pid} 2>/dev/null && echo 1 || echo 0");

        return trim($result) === '1';
    }
}
