<?php

/** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Unit\Containers\GenericContainer;

use PHPUnit\Framework\TestCase;
use Testcontainers\Containers\WaitStrategy\LogMessageWaitStrategy;
use stdClass;
use Testcontainers\Containers\ContainerInstance;
use Testcontainers\Containers\GenericContainer\GenericContainer;
use Testcontainers\Containers\PortStrategy\StaticPortStrategy;
use Testcontainers\Containers\WaitStrategy\WaitingTimeoutException;
use Testcontainers\Docker\Exception\PortConflictException;
use UnexpectedValueException;

final class GenericContainerTest extends TestCase
{
    /**
     * @return void
     */
    public function testStart()
    {
        $container = new GenericContainer('alpine:latest');
        $instance = $container->start();

        $this->assertInstanceOf(ContainerInstance::class, $instance);
        $this->assertNotEmpty($instance->getContainerId());
    }

    /**
     * @return void
     */
    public function testStartFailsOnPortConflictWithStaticPortStrategy()
    {
        $this->expectException(PortConflictException::class);

        $container1 = (new GenericContainer('alpine:latest'))
            ->withExposedPort(80)
            ->withPortStrategy(new StaticPortStrategy(50000))
            ->withCommands(['tail', '-f', '/dev/null'])
        ;

        $instance1 = $container1->start();

        $this->assertInstanceOf(ContainerInstance::class, $instance1);
        $this->assertEquals(50000, $instance1->getMappedPort(80));

        $container2 = (new GenericContainer('alpine:latest'))
            ->withExposedPort(80)
            ->withPortStrategy(new StaticPortStrategy(50000))
        ;

        $container2->start();
    }

    /**
     * @return void
     */
    public function testStartStopsContainerOnWaitStrategyFailure()
    {
        $container = (new GenericContainer('alpine:latest'))
            ->withCommands(['tail', '-f', '/dev/null'])
            ->withWaitStrategy((new LogMessageWaitStrategy())->withPattern('never emitted')->withTimeoutSeconds(1))
            ->withAutoRemoveOnExit(true)
        ;

        try {
            $container->start();
            $this->fail('Expected WaitingTimeoutException');
        } catch (WaitingTimeoutException $e) {
            // Expected -- container should have been stopped and removed (--rm)
            $this->assertTrue(strpos($e->getMessage(), 'timed out') !== false);
        }
    }

    /**
     * @dataProvider providerWithDockerClientRejectsInvalidInputs
     * @param stdClass $client
     * @return void
     */
    public function testWithDockerClientRejectsInvalidInputs($client)
    {
        $container = new GenericContainer('alpine');
        $this->expectException(UnexpectedValueException::class);

        $container->withDockerClient($client);
    }

    /**
     * @return array<string, array{stdClass}>
     */
    public static function providerWithDockerClientRejectsInvalidInputs()
    {
        return [
            'case 1' => [new stdClass()],
        ];
    }

    /**
     * @return void
     */
    public function testConstructRejectsArrayImage()
    {
        $this->expectException(UnexpectedValueException::class);

        new GenericContainer([]);
    }

    /**
     * @return void
     */
    public function testConstructRejectsEmptyImage()
    {
        $this->expectException(UnexpectedValueException::class);

        new GenericContainer('');
    }
}
