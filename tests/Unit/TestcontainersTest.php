<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Testcontainers\Containers\GenericContainer\GenericContainer;
use Testcontainers\Containers\ReuseMode;
use Testcontainers\Docker\DockerClient;
use Testcontainers\Logger\ConsoleLogger;
use Testcontainers\Testcontainers;
use Tests\Images\AlpineContainer;
use Tests\Images\DinD;

final class TestcontainersTest extends TestCase
{
    /**
     * @return void
     */
    public function testRunReusesDinDAndItsClient()
    {
        $first = Testcontainers::run(DinD::class);
        $second = Testcontainers::run(DinD::class);

        self::assertSame($first, $second);
        self::assertSame($first->getData(DockerClient::class), $second->getData(DockerClient::class));
        self::assertTrue($second->isRunning());
    }

    /**
     * @return void
     */
    public function testRunWithContainerClassString()
    {
        $instance = Testcontainers::run(AlpineContainer::class);
        $running = $instance->isRunning();

        self::assertTrue($running);
        $instance->stop();
    }

    /**
     * @return void
     */
    public function testRunWithContainerInstance()
    {
        $instance = Testcontainers::run(new AlpineContainer());
        $running = $instance->isRunning();

        self::assertTrue($running);
        $instance->stop();
    }

    /**
     * @return void
     */
    public function testRunWithReuseModeAddCreatesNewContainers()
    {
        $instance1 = Testcontainers::run(AlpineContainerWithAddMode::class);
        $instance2 = Testcontainers::run(AlpineContainerWithAddMode::class);

        // ADD mode should create new container each time
        self::assertNotSame(
            $instance1->getContainerId()->toString(),
            $instance2->getContainerId()->toString()
        );
        self::assertTrue($instance1->isRunning());
        self::assertTrue($instance2->isRunning());
        $instance2->stop();
        $instance1->stop();
    }

    /**
     * @return void
     */
    public function testRunWithReuseModeReuseReturnsSameContainer()
    {
        $instance1 = Testcontainers::run(AlpineContainerWithReuseMode::class);
        $instance2 = Testcontainers::run(AlpineContainerWithReuseMode::class);

        // REUSE mode should return same container
        self::assertSame(
            $instance1->getContainerId()->toString(),
            $instance2->getContainerId()->toString()
        );
        self::assertTrue($instance1->isRunning());
        $instance2->stop();
    }

    /**
     * @return void
     */
    public function testRunWithReuseModeRestartStopsExistingContainer()
    {
        $instance1 = Testcontainers::run(AlpineContainerWithRestartMode::class);
        $containerId1 = $instance1->getContainerId()->toString();

        $instance2 = Testcontainers::run(AlpineContainerWithRestartMode::class);
        $containerId2 = $instance2->getContainerId()->toString();

        // RESTART mode should create new container and stop old one
        self::assertNotSame($containerId1, $containerId2);
        self::assertFalse($instance1->isRunning());
        self::assertTrue($instance2->isRunning());
        $instance2->stop();
    }

    /**
     * @return void
     */
    public function testRunWithReuseModeFromInstanceMethod()
    {
        $container = (new GenericContainer('alpine:latest'))
            ->withAutoRemoveOnExit(true)
            ->withCommands(['tail', '-f', '/dev/null'])
            ->withStopTimeout(0)
            ->withReuseMode(ReuseMode::REUSE());

        $instance1 = Testcontainers::run($container);
        $instance2 = Testcontainers::run($container);

        // Same object with REUSE mode should return same container
        self::assertSame(
            $instance1->getContainerId()->toString(),
            $instance2->getContainerId()->toString()
        );
        $instance2->stop();
    }

    /**
     * @return void
     */
    public function testSetLoggerIsPassedToContainersWithoutTheirOwn()
    {
        $stream = fopen('php://memory', 'w+');
        self::assertNotFalse($stream);

        try {
            Testcontainers::setLogger(new ConsoleLogger($stream));
            $instance = Testcontainers::run(new AlpineContainer());

            rewind($stream);
            $contents = stream_get_contents($stream);
            self::assertNotFalse($contents);
            self::assertTrue(strpos($contents, 'Starting container') !== false);
            $instance->stop();
        } finally {
            Testcontainers::setLogger(null);
        }
    }

    /**
     * @return void
     */
    public function testRunPrefersContainerLoggerOverTestcontainersLogger()
    {
        $globalStream = fopen('php://memory', 'w+');
        self::assertNotFalse($globalStream);
        $containerStream = fopen('php://memory', 'w+');
        self::assertNotFalse($containerStream);

        try {
            Testcontainers::setLogger(new ConsoleLogger($globalStream));
            $instance = Testcontainers::run((new AlpineContainer())->withLogger(new ConsoleLogger($containerStream)));

            rewind($globalStream);
            rewind($containerStream);
            $contents = stream_get_contents($globalStream);
            self::assertNotFalse($contents);
            self::assertSame('', $contents);
            $contents = stream_get_contents($containerStream);
            self::assertNotFalse($contents);
            self::assertTrue(strpos($contents, 'Starting container') !== false);
            $instance->stop();
        } finally {
            Testcontainers::setLogger(null);
        }
    }

    /**
     * @return void
     */
    public function testRunPassesEnvironmentLoggerToContainersWithoutLogger()
    {
        $container = new AlpineContainer();

        try {
            putenv('TESTCONTAINERS_LOG_LEVEL=error');
            $instance = Testcontainers::run($container);

            self::assertInstanceOf(ConsoleLogger::class, $container->logger());
            $instance->stop();
        } finally {
            putenv('TESTCONTAINERS_LOG_LEVEL');
        }
    }

    /**
     * @return void
     */
    public function testRunPrefersContainerLoggerOverEnvironmentLogger()
    {
        $containerLogger = new ConsoleLogger(fopen('php://memory', 'w+'));
        $container = (new AlpineContainer())->withLogger($containerLogger);

        try {
            putenv('TESTCONTAINERS_LOG_LEVEL=error');
            $instance = Testcontainers::run($container);

            self::assertSame($containerLogger, $container->logger());
            $instance->stop();
        } finally {
            putenv('TESTCONTAINERS_LOG_LEVEL');
        }
    }

    /**
     * @return void
     */
    public function testSetLoggerTakesPrecedenceOverEnvironmentLogger()
    {
        $logger = new ConsoleLogger(fopen('php://memory', 'w+'));
        $container = new AlpineContainer();

        try {
            putenv('TESTCONTAINERS_LOG_LEVEL=error');
            Testcontainers::setLogger($logger);
            $instance = Testcontainers::run($container);

            self::assertSame($logger, $container->logger());
            $instance->stop();
        } finally {
            Testcontainers::setLogger(null);
            putenv('TESTCONTAINERS_LOG_LEVEL');
        }
    }
}

final class AlpineContainerWithAddMode extends GenericContainer
{
    protected static $IMAGE = 'alpine:latest';

    protected static $AUTO_REMOVE_ON_EXIT = true;

    protected static $STOP_TIMEOUT = 0;

    protected static $COMMANDS = ['tail', '-f', '/dev/null'];

    protected static $REUSE_MODE = 'add';
}

final class AlpineContainerWithReuseMode extends GenericContainer
{
    protected static $IMAGE = 'alpine:latest';

    protected static $AUTO_REMOVE_ON_EXIT = true;

    protected static $STOP_TIMEOUT = 0;

    protected static $COMMANDS = ['tail', '-f', '/dev/null'];

    protected static $REUSE_MODE = 'reuse';
}

final class AlpineContainerWithRestartMode extends GenericContainer
{
    protected static $IMAGE = 'alpine:latest';

    protected static $AUTO_REMOVE_ON_EXIT = true;

    protected static $STOP_TIMEOUT = 0;

    protected static $COMMANDS = ['tail', '-f', '/dev/null'];

    protected static $REUSE_MODE = 'restart';
}
