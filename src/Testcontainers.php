<?php

namespace Testcontainers;

use LogicException;
use Testcontainers\Containers\Container;
use Testcontainers\Containers\ContainerInstance;

/**
 * Class Testcontainers
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
     * List of started containers
     *
     * @var ContainerInstance[] $instances
     */
    private static $instances = [];

    /**
     * Flag to ensure that the shutdown handler is set only once
     *
     * @var bool $setOnceShutdownHandler
     */
    private static $setOnceShutdownHandler = false;

    /**
     * Run a container
     *
     *　This method initializes and starts a container of the specified class.
     *  The container class must extend GenericContainer.
     *  If the container class has `beforeStart` and `afterStart` methods, they will be called appropriately.
     *
     * @param class-string<Container>|Container $containerClass The class name of the container to run.
     * @return ContainerInstance
     */
    public static function run($containerClass)
    {
        if (is_string($containerClass) && class_exists($containerClass)) {
            $container = new $containerClass();
        } elseif ($containerClass instanceof Container) {
            $container = $containerClass;
        } else {
            throw new LogicException('The container class must be a valid class name or an instance of `Container`');
        }

        if (!($container instanceof Container)) {
            throw new LogicException('The container class must be a valid class name or an instance of `Container`');
        }

        if (method_exists($container, 'beforeStart')) {
            $container->beforeStart();
        }

        $instance = $container->start();
        self::$instances[] = $instance;

        self::registerOnceShutdownHandler();

        if (method_exists($container, 'afterStart')) {
            $container->afterStart($instance);
        }

        return $instance;
    }

    /**
     * Stop all started containers
     *
     * This method stops all containers that were started using the `run` method.
     * It iterates over the list of started containers and calls the `stop` method on each instance.
     *
     * @return void
     */
    public static function stop()
    {
        foreach (self::$instances as $instance) {
            $instance->stop();
        }
        self::$instances = [];
    }

    /**
     * Stop all started containers when the script ends
     *
     * @return void
     */
    private static function registerOnceShutdownHandler()
    {
        if (self::$setOnceShutdownHandler === false) {
            register_shutdown_function(function () {
                self::stop();
            });
            self::$setOnceShutdownHandler = true;
        }
    }
}
