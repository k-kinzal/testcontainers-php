<?php

namespace Testcontainers;

use Exception;
use LogicException;
use Testcontainers\Containers\Container;
use Testcontainers\Containers\ContainerInstance;
use Testcontainers\Containers\StartupCheckStrategy\StartupCheckFailedException;
use Testcontainers\Docker\DockerClientFactory;
use Testcontainers\Docker\Exception\DockerException;
use Testcontainers\Exceptions\ContainerStopException;
use Testcontainers\Exceptions\InvalidFormatException;
use Testcontainers\Lifecycle\ContainerReaper;
use Testcontainers\Lifecycle\ShutdownHandler;

use function Testcontainers\ensure;

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
     *
     * @throws InvalidFormatException      if a configuration value is not valid
     * @throws DockerException             if the Docker command fails
     * @throws StartupCheckFailedException if the container fails to start within the timeout
     */
    public static function run($containerClass)
    {
        ensure(
            is_string($containerClass) || $containerClass instanceof Container,
            '$containerClass must be string|Container'
        );

        if (is_string($containerClass) && class_exists($containerClass)) {
            $container = new $containerClass();
        } elseif ($containerClass instanceof Container) {
            $container = $containerClass;
        } else {
            throw new LogicException('The container class must be a valid class name or an instance of `Container`');
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
            $reaper = new ContainerReaper(DockerClientFactory::create());
            $reaper->execute();
            self::$cleanupDone = true;
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
     *
     * @return void
     *
     * @throws ContainerStopException if one or more containers fail to stop
     */
    public static function stop()
    {
        $errors = [];
        $stopped = [];
        foreach (self::$instances as $key => $instance) {
            try {
                $instance->stop();
                $stopped[] = $key;
            } catch (Exception $e) {
                $errors[$key] = $e;
            }
        }
        foreach ($stopped as $key) {
            unset(self::$instances[$key]);
        }
        if (!empty($errors)) {
            throw new ContainerStopException($errors);
        }
    }

}
