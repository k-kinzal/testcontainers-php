<?php

/** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Unit\Containers;

use PHPUnit\Framework\TestCase;
use stdClass;
use Testcontainers\Containers\ContainerInstance;
use Testcontainers\Containers\GenericContainer\GenericContainer;
use Testcontainers\Containers\PortStrategy\StaticPortStrategy;
use Testcontainers\Containers\WaitStrategy\WaitingTimeoutException;
use Testcontainers\Docker\Exception\PortConflictException;
use UnexpectedValueException;

/**
 * @internal
 *
 * @coversNothing
 */
class GenericContainerTest extends TestCase
{
    public function testStart()
    {
        $container = new GenericContainer('alpine:latest');
        $instance = $container->start();

        $this->assertInstanceOf(ContainerInstance::class, $instance);
        $this->assertNotEmpty($instance->getContainerId());
    }

    public function testContainerFailsOnPortConflictWithStaticPortStrategy()
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

    public function testStartStopsContainerOnWaitStrategyFailure()
    {
        $container = (new GenericContainer('alpine:latest'))
            ->withCommands(['tail', '-f', '/dev/null'])
            ->withWaitStrategy(new FailingWaitStrategy())
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
     * @dataProvider provideWithDockerClientInvalidInputs
     * @param mixed $client
     */
    public function testWithDockerClientRejectsInvalidInputs($client)
    {
        $container = new GenericContainer('alpine');
        $this->expectException(UnexpectedValueException::class);

        $container->withDockerClient($client);
    }

    /**
     * @return array
     */
    public static function provideWithDockerClientInvalidInputs()
    {
        return [
            'case 1' => [new stdClass()],
        ];
    }

    public function test__constructRejectsArrayImage()
    {
        $this->expectException(UnexpectedValueException::class);

        new GenericContainer([]);
    }

    public function test__constructRejectsEmptyImage()
    {
        $this->expectException(UnexpectedValueException::class);

        new GenericContainer('');
    }
}

class FailingWaitStrategy implements \Testcontainers\Containers\WaitStrategy\WaitStrategy
{
    use \Testcontainers\Logger\WithLogger;

    public function waitUntilReady($instance)
    {
        throw new WaitingTimeoutException(0, 'intentional failure for testing');
    }
}
