<?php

namespace Testcontainers;

use Testcontainers\Utility\Ensure;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Testcontainers\Containers\Container;
use Testcontainers\Containers\ContainerInstance;
use Testcontainers\Docker\DockerClientFactory;
use Testcontainers\Lifecycle\ContainerReaper;
use Testcontainers\Lifecycle\ShutdownHandler;
use Testcontainers\Logger\LoggerFactory;

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
 * @api
 */
class Testcontainers
{
    use Ensure;

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
     * Logger to be passed to started containers.
     *
     * @var null|LoggerInterface
     */
    private static $logger;

    /**
     * Set the logger for containers.
     *
     * This method sets the logger that is passed to containers started by the `run` method.
     * The logger is not passed to containers that have their own logger.
     *
     * @param null|LoggerInterface $logger the logger instance
     * @psalm-param mixed $logger
     */
    public static function setLogger($logger)
    {
        if ($logger !== null) {
            self::ensureInstanceOf($logger, LoggerInterface::class);

        }

        self::$logger = $logger;
    }

    /**
     * Run a container.
     *
     *　This method initializes and starts a container of the specified class.
     *  The container class must extend GenericContainer.
     *  If the container class has `beforeStart` and `afterStart` methods, they will be called appropriately.
     *  If the container has no logger, the logger set by `setLogger` or specified by environment variables is passed to the container.
     *
     * @param class-string<Container>|Container $containerClass the class name of the container to run
     * @psalm-param mixed $containerClass
     *
     * @return ContainerInstance
     */
    public static function run($containerClass)
    {
        if (is_string($containerClass) && is_a($containerClass, Container::class, true)) {
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

        if ($container->logger() instanceof NullLogger) {
            $container->withLogger(self::$logger ?: LoggerFactory::create());
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
     */
    public static function stop()
    {
        $errors = [];
        $stopped = [];
        foreach (self::$instances as $key => $instance) {
            try {
                $instance->stop();
                $stopped[] = $key;
            } catch (\Exception $e) {
                $errors[$key] = $e;
            }
        }
        foreach ($stopped as $key) {
            unset(self::$instances[$key]);
        }
        if (!empty($errors)) {
            throw new \Testcontainers\Exceptions\ContainerStopException($errors);
        }
    }

}
