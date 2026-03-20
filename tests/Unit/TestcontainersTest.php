<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Testcontainers\Containers\ContainerInstance;
use Testcontainers\Containers\GenericContainer\GenericContainer;
use Testcontainers\Containers\ReuseMode;
use Testcontainers\Exceptions\ContainerStopException;
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

    public function testStopRemovesSucceededAndKeepsFailed()
    {
        $throwingInstance = $this->createMock(ContainerInstance::class);
        $throwingInstance->method('stop')->willThrowException(new \RuntimeException('stop failed'));

        $normalInstance = $this->createMock(ContainerInstance::class);
        $normalInstance->expects($this->once())->method('stop');

        $ref = new \ReflectionClass(Testcontainers::class);
        $prop = $ref->getProperty('instances');
        $prop->setValue(null, ['throwing' => $throwingInstance, 'normal' => $normalInstance]);

        try {
            Testcontainers::stop();
            $this->fail('Expected ContainerStopException');
        } catch (ContainerStopException $e) {
            $errors = $e->getErrors();
            $this->assertCount(1, $errors);
            $this->assertArrayHasKey('throwing', $errors);
            $this->assertSame('stop failed', $errors['throwing']->getMessage());
        }

        // Failed instance remains, succeeded instance is removed
        $remaining = $prop->getValue();
        $this->assertCount(1, $remaining);
        $this->assertArrayHasKey('throwing', $remaining);
        $this->assertArrayNotHasKey('normal', $remaining);

        // Clean up for other tests
        $prop->setValue(null, []);
    }

    public function testStopAllSuccessNoException()
    {
        $instance1 = $this->createMock(ContainerInstance::class);
        $instance1->expects($this->once())->method('stop');

        $instance2 = $this->createMock(ContainerInstance::class);
        $instance2->expects($this->once())->method('stop');

        $ref = new \ReflectionClass(Testcontainers::class);
        $prop = $ref->getProperty('instances');
        $prop->setValue(null, ['a' => $instance1, 'b' => $instance2]);

        Testcontainers::stop();

        $this->assertSame([], $prop->getValue());
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
