<?php

namespace Testcontainers;

use Testcontainers\Containers\Container;
use Testcontainers\Containers\ContainerInstance;
use Testcontainers\Docker\DockerClientFactory;
use Testcontainers\Lifecycle\ShutdownHandler;

/**
 * Class Testcontainers.
 *
 * This class provides methods to manage the lifecycle of Docker containers for testing purposes.
 * It allows you to start and stop containers programmatically.
 *
 * Example usage with PHPUnit:
 *
 * ```
 * use PHPUnit\Framework\TestCase;
 * use Testcontainers\Testcontainers;
 * use Testcontainers\Containers\GenericContainer;
 *
 * class MyTest extends TestCase
 * {
 *     public function tearDown()
 *     {
 *         Testcontainers::stop();
 *     }
 *
 *     public function test()
 *     {
 *         Testcontainers::run(MyContainer::class);
 *
 *         // Your test code here
 *     }
 * }
 *
 * class MyContainer extends GenericContainer
 * {
 *     protected static $IMAGE = 'alpine:3.10';
 * }
 * ```
 */
class Testcontainers
{
    /**
     * Map of started containers by identifier.
     *
     * @var array<string, ContainerInstance>
     */
    private static $instances = [];

    /**
     * Flag to ensure cleanup runs only once per process.
     *
     * @var bool
     */
    private static $cleanupDone = false;

    /**
     * Run a container.
     *
     *　This method initializes and starts a container of the specified class.
     *  The container class must extend GenericContainer.
     *  If the container class has `beforeStart` and `afterStart` methods, they will be called appropriately.
     *
     * @param class-string<Container>|Container $containerClass the class name of the container to run
     *
     * @return ContainerInstance
     */
    public static function run($containerClass)
    {
        if (is_string($containerClass) && class_exists($containerClass)) {
            $container = new $containerClass();
        } elseif ($containerClass instanceof Container) {
            $container = $containerClass;
        } else {
            throw new \LogicException('The container class must be a valid class name or an instance of `Container`');
        }

        $identifier = is_string($containerClass)
            ? $containerClass
            : spl_object_hash($containerClass);
        $reuseMode = $container->reuseMode();

        if ($reuseMode->isReuse() && isset(self::$instances[$identifier])) {
            if (self::$instances[$identifier]->isRunning()) {
                return self::$instances[$identifier];
            }
        }
        if ($reuseMode->isRestart() && isset(self::$instances[$identifier])) {
            self::$instances[$identifier]->stop();
        }

        ShutdownHandler::register([self::class, 'stop']);

        if (!self::$cleanupDone) {
            self::cleanup();
        }

        if (method_exists($container, 'beforeStart')) {
            $container->beforeStart();
        }

        $instance = $container->start();
        self::$instances[$identifier] = $instance;

        if (method_exists($container, 'afterStart')) {
            $container->afterStart($instance);
        }

        return $instance;
    }

    /**
     * Stop all started containers.
     *
     * This method stops all containers that were started using the `run` method.
     * It iterates over the list of started containers and calls the `stop` method on each instance.
     */
    public static function stop()
    {
        foreach (self::$instances as $instance) {
            try {
                $instance->stop();
            } catch (\Exception $e) {
                // Continue stopping remaining containers even if one fails
            }
        }
        self::$instances = [];
    }

    /**
     * Clean up orphaned containers from crashed processes.
     *
     * This method finds all containers labeled with `org.testcontainers=true`,
     * checks if the owning process (stored in `org.testcontainers.pid` label) is still alive,
     * and removes containers whose owning process has died.
     *
     * Safe for concurrent use: containers owned by other running processes are never touched.
     */
    public static function cleanup()
    {
        try {
            $client = DockerClientFactory::create();
            $output = $client->ps([
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
                if ($pid !== null && self::isProcessAlive((int) $pid)) {
                    continue;
                }

                // Owning process is dead -- container is orphaned, stop it.
                // Removal is left to Docker's --rm flag or explicit user action.
                try {
                    $client->stop($container->id);
                } catch (\Exception $e) {
                    // Container may already be stopped
                }
            }
        } catch (\Exception $e) {
            // Cleanup is best-effort; don't fail the test run
        }

        self::$cleanupDone = true;
    }

    /**
     * Check if a process with the given PID is still alive.
     *
     * @param int $pid the process ID to check
     *
     * @return bool true if the process is alive, false otherwise
     */
    private static function isProcessAlive($pid)
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
