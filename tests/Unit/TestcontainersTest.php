<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Testcontainers\Containers\GenericContainer\GenericContainer;
use Testcontainers\Containers\ReuseMode;
use Testcontainers\Logger\ConsoleLogger;
use Testcontainers\Testcontainers;
use Tests\Images\AlpineContainer;

/**
 * @internal
 *
 * @coversNothing
 */
class TestcontainersTest extends TestCase
{
    public function testWithContainerClassString()
    {
        try {
            $instance = Testcontainers::run(AlpineContainer::class);
            $running = $instance->isRunning();

            $this->assertTrue($running);
        } finally {
            Testcontainers::stop();
        }
    }

    public function testWithContainerInstance()
    {
        try {
            $instance = Testcontainers::run(new AlpineContainer());
            $running = $instance->isRunning();

            $this->assertTrue($running);
        } finally {
            Testcontainers::stop();
        }
    }

    public function testReuseModeAddCreatesNewContainers()
    {
        try {
            $instance1 = Testcontainers::run(AlpineContainerWithAddMode::class);
            $instance2 = Testcontainers::run(AlpineContainerWithAddMode::class);

            // ADD mode should create new container each time
            $this->assertNotSame(
                $instance1->getContainerId()->toString(),
                $instance2->getContainerId()->toString()
            );
            $this->assertTrue($instance1->isRunning());
            $this->assertTrue($instance2->isRunning());
        } finally {
            Testcontainers::stop();
        }
    }

    public function testReuseModeReuseReturnsSameContainer()
    {
        try {
            $instance1 = Testcontainers::run(AlpineContainerWithReuseMode::class);
            $instance2 = Testcontainers::run(AlpineContainerWithReuseMode::class);

            // REUSE mode should return same container
            $this->assertSame(
                $instance1->getContainerId()->toString(),
                $instance2->getContainerId()->toString()
            );
            $this->assertTrue($instance1->isRunning());
        } finally {
            Testcontainers::stop();
        }
    }

    public function testReuseModeRestartStopsExistingContainer()
    {
        try {
            $instance1 = Testcontainers::run(AlpineContainerWithRestartMode::class);
            $containerId1 = $instance1->getContainerId()->toString();

            $instance2 = Testcontainers::run(AlpineContainerWithRestartMode::class);
            $containerId2 = $instance2->getContainerId()->toString();

            // RESTART mode should create new container and stop old one
            $this->assertNotSame($containerId1, $containerId2);
            $this->assertFalse($instance1->isRunning());
            $this->assertTrue($instance2->isRunning());
        } finally {
            Testcontainers::stop();
        }
    }

    public function testReuseModeWithInstanceMethod()
    {
        try {
            $container = (new GenericContainer('alpine:latest'))
                ->withCommands(['tail', '-f', '/dev/null'])
                ->withReuseMode(ReuseMode::REUSE());

            $instance1 = Testcontainers::run($container);
            $instance2 = Testcontainers::run($container);

            // Same object with REUSE mode should return same container
            $this->assertSame(
                $instance1->getContainerId()->toString(),
                $instance2->getContainerId()->toString()
            );
        } finally {
            Testcontainers::stop();
        }
    }

    public function testLoggerIsPassedToContainersWithoutTheirOwn()
    {
        $stream = fopen('php://memory', 'w+');

        try {
            Testcontainers::setLogger(new ConsoleLogger($stream));
            Testcontainers::run(new AlpineContainer());

            rewind($stream);
            $this->assertTrue(strpos(stream_get_contents($stream), 'Starting container') !== false);
        } finally {
            Testcontainers::stop();
            Testcontainers::setLogger(null);
        }
    }

    public function testContainerLoggerTakesPrecedenceOverTestcontainersLogger()
    {
        $globalStream = fopen('php://memory', 'w+');
        $containerStream = fopen('php://memory', 'w+');

        try {
            Testcontainers::setLogger(new ConsoleLogger($globalStream));
            Testcontainers::run((new AlpineContainer())->withLogger(new ConsoleLogger($containerStream)));

            rewind($globalStream);
            rewind($containerStream);
            $this->assertSame('', stream_get_contents($globalStream));
            $this->assertTrue(strpos(stream_get_contents($containerStream), 'Starting container') !== false);
        } finally {
            Testcontainers::stop();
            Testcontainers::setLogger(null);
        }
    }

    public function testEnvironmentLoggerIsPassedToContainersWithoutLogger()
    {
        $container = new AlpineContainer();

        try {
            putenv('TESTCONTAINERS_LOG_LEVEL=error');
            Testcontainers::run($container);

            $this->assertInstanceOf(ConsoleLogger::class, $container->logger());
        } finally {
            Testcontainers::stop();
            putenv('TESTCONTAINERS_LOG_LEVEL');
        }
    }

    public function testContainerLoggerTakesPrecedenceOverEnvironmentLogger()
    {
        $containerLogger = new ConsoleLogger(fopen('php://memory', 'w+'));
        $container = (new AlpineContainer())->withLogger($containerLogger);

        try {
            putenv('TESTCONTAINERS_LOG_LEVEL=error');
            Testcontainers::run($container);

            $this->assertSame($containerLogger, $container->logger());
        } finally {
            Testcontainers::stop();
            putenv('TESTCONTAINERS_LOG_LEVEL');
        }
    }

    public function testTestcontainersLoggerTakesPrecedenceOverEnvironmentLogger()
    {
        $logger = new ConsoleLogger(fopen('php://memory', 'w+'));
        $container = new AlpineContainer();

        try {
            putenv('TESTCONTAINERS_LOG_LEVEL=error');
            Testcontainers::setLogger($logger);
            Testcontainers::run($container);

            $this->assertSame($logger, $container->logger());
        } finally {
            Testcontainers::stop();
            Testcontainers::setLogger(null);
            putenv('TESTCONTAINERS_LOG_LEVEL');
        }
    }
}

class AlpineContainerWithAddMode extends GenericContainer
{
    protected static $IMAGE = 'alpine:latest';

    protected static $COMMANDS = ['tail', '-f', '/dev/null'];

    protected static $REUSE_MODE = 'add';
}

class AlpineContainerWithReuseMode extends GenericContainer
{
    protected static $IMAGE = 'alpine:latest';

    protected static $COMMANDS = ['tail', '-f', '/dev/null'];

    protected static $REUSE_MODE = 'reuse';
}

class AlpineContainerWithRestartMode extends GenericContainer
{
    protected static $IMAGE = 'alpine:latest';

    protected static $COMMANDS = ['tail', '-f', '/dev/null'];

    protected static $REUSE_MODE = 'restart';
}
