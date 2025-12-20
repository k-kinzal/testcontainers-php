<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Testcontainers\Containers\GenericContainer\GenericContainer;
use Testcontainers\Containers\ReuseMode;
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
