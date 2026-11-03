<?php

/** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Unit\Containers\GenericContainer;

use PHPUnit\Framework\TestCase;
use stdClass;
use Closure;
use Testcontainers\Docker\Types\Event;
use Symfony\Component\Process\Process;
use Testcontainers\Containers\GenericContainer\GenericContainer;
use Testcontainers\Containers\GenericContainer\GenericContainerInstance;
use Testcontainers\Containers\Types\ImagePullPolicy;
use Testcontainers\Docker\DockerClient;
use Testcontainers\Docker\Exception\DockerException;
use Testcontainers\Docker\Output\DockerRunWithDetachOutput;
use Testcontainers\Docker\Types\ContainerId;
use Testcontainers\Logger\ConsoleLogger;
use Testcontainers\SSH\Session;
use Testcontainers\Testcontainers;
use Tests\Images\DinD;

final class GenericContainerInstanceTest extends TestCase
{
    /**
     * @return void
     */
    public function testGetContainerId()
    {
        $instance = new GenericContainerInstance([
            'containerId' => new ContainerId('8188d93d8a27'),
        ]);

        self::assertSame('8188d93d8a27', (string) $instance->getContainerId());
    }

    /**
     * @return void
     */
    public function testGetLabel()
    {
        $instance = new GenericContainerInstance([
            'containerId' => new ContainerId('8188d93d8a27'),
            'labels' => [
                'com.example.label' => 'value',
            ],
        ]);

        self::assertSame('value', $instance->getLabel('com.example.label'));
    }

    /**
     * @return void
     */
    public function testGetLabels()
    {
        $instance = new GenericContainerInstance([
            'containerId' => new ContainerId('8188d93d8a27'),
            'labels' => [
                'com.example.label' => 'value',
            ],
        ]);

        self::assertSame(['com.example.label' => 'value'], $instance->getLabels());
    }

    /**
     * @return void
     */
    public function testGetHost()
    {
        $instance = new GenericContainerInstance([
            'containerId' => new ContainerId('8188d93d8a27'),
        ]);

        self::assertSame('localhost', $instance->getHost());
    }

    /**
     * @return void
     */
    public function testGetHostFromDockerHost()
    {
        $dind = Testcontainers::run(DinD::class);
        $client = $dind->getData(DockerClient::class);

        $instance = new GenericContainerInstance([
            'containerId' => new ContainerId('8188d93d8a27'),
        ]);
        $instance->setDockerClient($client);

        self::assertSame($dind->getHost(), $instance->getHost());
    }

    /**
     * @return void
     */
    public function testGetHostFromPortForward()
    {
        $dind = Testcontainers::run(DinD::class);
        $client = $dind->getData(DockerClient::class);

        $instance = new GenericContainerInstance([
            'containerId' => new ContainerId('8188d93d8a27'),
        ]);
        $instance->setDockerClient($client);
        $instance->setData(new Session(new Process(['pwd'])));

        self::assertSame('localhost', $instance->getHost());
    }

    /**
     * @return void
     */
    public function testGetHostFromOverride()
    {
        try {
            putenv('TESTCONTAINERS_HOST_OVERRIDE=override.local');
            $instance = new GenericContainerInstance([
                'containerId' => new ContainerId('8188d93d8a27'),
            ]);

            self::assertSame('override.local', $instance->getHost());
        } finally {
            putenv('TESTCONTAINERS_HOST_OVERRIDE');
        }
    }

    /**
     * @return void
     */
    public function testGetMappedPort()
    {
        $instance = new GenericContainerInstance([
            'containerId' => new ContainerId('8188d93d8a27'),
            'ports' => [
                80 => 8080,
            ],
        ]);

        self::assertSame(8080, $instance->getMappedPort(80));
    }

    /**
     * @return void
     */
    public function testGetImagePullPolicy()
    {
        $pullPolicy = ImagePullPolicy::ALWAYS();
        $instance = new GenericContainerInstance([
            'containerId' => new ContainerId('8188d93d8a27'),
            'pull' => $pullPolicy,
        ]);

        self::assertSame($pullPolicy, $instance->getImagePullPolicy());
    }

    /**
     * @return void
     */
    public function testGetPrivilegedMode()
    {
        $instance = new GenericContainerInstance([
            'containerId' => new ContainerId('8188d93d8a27'),
            'privileged' => true,
        ]);

        self::assertTrue($instance->getPrivilegedMode());
    }

    /**
     * @return void
     */
    public function testGetOutput()
    {
        $container = (new GenericContainer('alpine:latest'))
            ->withAutoRemoveOnExit(false)
            ->withCommands(['echo', 'Hello, World!'])
        ;
        $instance = $container->start();

        $wait = new Process(['docker', 'wait', $instance->getContainerId()->toString()]);
        self::assertSame(0, $wait->run());
        self::assertSame("Hello, World!\n", $instance->getOutput());
    }

    /**
     * @return void
     */
    public function testGetErrorOutput()
    {
        $container = (new GenericContainer('alpine:latest'))
            ->withAutoRemoveOnExit(false)
            ->withCommands(['ls', '/not-exist-dir'])
        ;
        $instance = $container->start();

        $wait = new Process(['docker', 'wait', $instance->getContainerId()->toString()]);
        self::assertSame(0, $wait->run());
        self::assertSame("ls: /not-exist-dir: No such file or directory\n", $instance->getErrorOutput());
    }

    /**
     * @return void
     */
    public function testSetDataAndGetData()
    {
        $data = new stdClass();

        $instance = new GenericContainerInstance([
            'containerId' => new ContainerId('8188d93d8a27'),
        ]);
        $instance->setData($data);

        self::assertSame($data, $instance->getData(stdClass::class));
    }

    /**
     * @return void
     */
    public function testIsRunning()
    {
        $instance = new GenericContainerInstance([
            'containerId' => new ContainerId('8188d93d8a27'),
        ]);

        self::assertFalse($instance->isRunning());
    }

    /**
     * @return void
     */
    public function testIsRunningReusesInspectedStateUntilEventIsEmitted()
    {
        $stream = fopen('php://memory', 'w+');
        self::assertNotFalse($stream);
        $logger = new ConsoleLogger($stream);
        $client = (new DockerClient())->withLogger($logger);
        $container = (new GenericContainer('alpine:latest'))
            ->withLogger($logger)
            ->withDockerClient($client)
            ->withCommands(['sleep', '300'])
            ->withInspectInterval(300)
            ->withStopTimeout(0)
        ;
        $instance = $container->start();
        self::assertTrue($instance->isRunning());
        $events = call_user_func(function () {
            yield null;
            yield null;
            yield null;
            yield Event::fromArray(['Type' => 'container', 'Action' => 'die', 'Actor' => ['ID' => 'fixture', 'Attributes' => null]]);
            yield null;
            yield null;
        });
        $useEvents = Closure::bind(function () use ($events) {
            /** @psalm-scope-this GenericContainerInstance */
            $this->events = $events;
            $this->eventObserved = true;
        }, $instance, GenericContainerInstance::class);
        self::assertNotNull($useEvents);
        $useEvents();
        rewind($stream);
        $contents = stream_get_contents($stream);
        self::assertNotFalse($contents);
        $inspected = substr_count($contents, 'docker inspect ');

        self::assertTrue($instance->isRunning());
        self::assertTrue($instance->isRunning());
        rewind($stream);
        $contents = stream_get_contents($stream);
        self::assertNotFalse($contents);
        self::assertSame($inspected, substr_count($contents, 'docker inspect '));

        $client->stop($instance->getContainerId(), ['timeout' => 0]);
        self::assertFalse($instance->isRunning());
        self::assertTrue($instance->isStopped());
        rewind($stream);
        $contents = stream_get_contents($stream);
        self::assertNotFalse($contents);
        self::assertGreaterThan($inspected, substr_count($contents, 'docker inspect '));
    }

    /**
     * @return void
     */
    public function testIsRunningInspectsWithoutEvents()
    {
        $stream = fopen('php://memory', 'w+');
        self::assertNotFalse($stream);
        $logger = new ConsoleLogger($stream);
        $client = (new DockerClient())->withLogger($logger);
        $container = (new GenericContainer('alpine:latest'))
            ->withLogger($logger)
            ->withDockerClient($client)
            ->withCommands(['sleep', '300'])
            ->withInspectInterval(10)
            ->withStopTimeout(0)
        ;
        $instance = $container->start();
        $clearEvents = Closure::bind(function () {
            /** @psalm-scope-this GenericContainerInstance */
            $this->events = null;
        }, $instance, GenericContainerInstance::class);
        self::assertNotNull($clearEvents);
        $clearEvents();
        rewind($stream);
        $contents = stream_get_contents($stream);
        self::assertNotFalse($contents);
        $inspected = substr_count($contents, 'docker inspect ');

        self::assertTrue($instance->isRunning());
        self::assertTrue($instance->isRunning());
        rewind($stream);
        $contents = stream_get_contents($stream);
        self::assertNotFalse($contents);
        self::assertSame($inspected + 2, substr_count($contents, 'docker inspect '));
    }

    /**
     * @return void
     */
    public function testIsRunningInspectsAfterInspectIntervalElapses()
    {
        $stream = fopen('php://memory', 'w+');
        self::assertNotFalse($stream);
        $logger = new ConsoleLogger($stream);
        $client = (new DockerClient())->withLogger($logger);
        $container = (new GenericContainer('alpine:latest'))
            ->withLogger($logger)
            ->withDockerClient($client)
            ->withCommands(['sleep', '300'])
            ->withInspectInterval(0.5)
            ->withStopTimeout(0)
        ;
        $instance = $container->start();
        self::assertTrue($instance->isRunning());
        $events = call_user_func(function () {
            yield null;
            yield null;
            yield null;
            yield null;
            yield null;
        });
        $useEvents = Closure::bind(function () use ($events) {
            /** @psalm-scope-this GenericContainerInstance */
            $this->events = $events;
            $this->eventObserved = true;
        }, $instance, GenericContainerInstance::class);
        self::assertNotNull($useEvents);
        $useEvents();
        rewind($stream);
        $contents = stream_get_contents($stream);
        self::assertNotFalse($contents);
        $inspected = substr_count($contents, 'docker inspect ');

        usleep(500000);

        self::assertTrue($instance->isRunning());
        rewind($stream);
        $contents = stream_get_contents($stream);
        self::assertNotFalse($contents);
        self::assertSame($inspected + 1, substr_count($contents, 'docker inspect '));
    }

    /**
     * @return void
     */
    public function testIsRunningInspectsEveryTimeWithInspectIntervalOfZero()
    {
        $stream = fopen('php://memory', 'w+');
        self::assertNotFalse($stream);
        $logger = new ConsoleLogger($stream);
        $client = (new DockerClient())->withLogger($logger);
        $container = (new GenericContainer('alpine:latest'))
            ->withLogger($logger)
            ->withDockerClient($client)
            ->withCommands(['sleep', '300'])
            ->withInspectInterval(0)
            ->withStopTimeout(0)
        ;
        $instance = $container->start();
        rewind($stream);
        $contents = stream_get_contents($stream);
        self::assertNotFalse($contents);
        $inspected = substr_count($contents, 'docker inspect ');

        self::assertTrue($instance->isRunning());
        self::assertTrue($instance->isRunning());
        rewind($stream);
        $contents = stream_get_contents($stream);
        self::assertNotFalse($contents);
        self::assertSame($inspected + 2, substr_count($contents, 'docker inspect '));
    }

    /**
     * @return void
     */
    public function testIsStopped()
    {
        $client = new DockerClient();

        /** @var DockerRunWithDetachOutput $output */
        $output = $client->run('alpine:latest', 'sleep', ['10'], [
            'detach' => true,
        ]);
        $instance = new GenericContainerInstance([
            'containerId' => $output->getContainerId(),
        ]);

        self::assertFalse($instance->isStopped());

        $instance->stop();

        self::assertTrue($instance->isStopped());

        $client->rm($output->getContainerId());

        self::assertTrue($instance->isStopped());
    }

    /**
     * @return void
     */
    public function testGetState()
    {
        $client = new DockerClient();
        $container = (new GenericContainer('alpine:latest'))
            ->withDockerClient($client)
            ->withCommands(['sleep', '300'])
            ->withInspectInterval(10)
            ->withStopTimeout(0)
        ;
        $instance = $container->start();
        self::assertInstanceOf(GenericContainerInstance::class, $instance);

        $state = $instance->getState();
        self::assertNotNull($state);
        self::assertSame('running', $state->status);
    }

    /**
     * @return void
     */
    public function testGetStateIfContainerDoesNotExist()
    {
        $instance = new GenericContainerInstance([
            'containerId' => new ContainerId('8188d93d8a27'),
        ]);

        self::assertNull($instance->getState());
    }

    /**
     * @return void
     */
    public function testIsStoppedIfContainerDoesNotExist()
    {
        $instance = new GenericContainerInstance([
            'containerId' => new ContainerId('8188d93d8a27'),
        ]);

        self::assertTrue($instance->isStopped());
    }

    /**
     * @return void
     */
    public function testGetStateSubscribesEventsOfContainer()
    {
        $stream = fopen('php://memory', 'w+');
        self::assertNotFalse($stream);
        $logger = new ConsoleLogger($stream);
        $client = (new DockerClient())->withLogger($logger);
        $container = (new GenericContainer('alpine:latest'))
            ->withLogger($logger)
            ->withDockerClient($client)
            ->withCommands(['sleep', '300'])
            ->withInspectInterval(10)
            ->withStopTimeout(0)
        ;
        $instance = $container->start();
        self::assertInstanceOf(GenericContainerInstance::class, $instance);

        $instance->getState();
        $instance->getState();

        rewind($stream);
        $log = stream_get_contents($stream);
        self::assertNotFalse($log);
        self::assertSame(1, substr_count($log, 'docker events '));
        self::assertNotFalse(strpos($log, 'container='.$instance->getContainerId()->toString()));
        self::assertNotFalse(strpos($log, 'event=die'));
        self::assertNotFalse(strpos($log, '--since 0'));
    }

    /**
     * @return void
     */
    public function testStopStopsObservingEvents()
    {
        $client = new DockerClient();
        $container = (new GenericContainer('alpine:latest'))
            ->withDockerClient($client)
            ->withCommands(['sleep', '300'])
            ->withInspectInterval(10)
            ->withStopTimeout(0)
        ;
        $instance = $container->start();
        self::assertTrue($instance->isRunning());
        $properties = (array) $instance;
        self::assertNotNull($properties["\0".GenericContainerInstance::class."\0events"]);

        $instance->stop();

        $properties = (array) $instance;
        self::assertNull($properties["\0".GenericContainerInstance::class."\0events"]);
    }

    /**
     * @return void
     */
    public function testStop()
    {
        $instance = new GenericContainerInstance([
            'containerId' => new ContainerId('8188d93d8a27'),
        ]);
        $instance->stop();

        self::assertFalse($instance->isRunning());
    }

    /**
     * @return void
     */
    public function testStopThrowsDockerException()
    {
        $client = (new DockerClient())->withGlobalOptions([
            'host' => 'unix://'.__DIR__.'/missing-docker.sock',
        ]);
        $instance = new GenericContainerInstance([
            'containerId' => new ContainerId('8188d93d8a27'),
        ]);
        $instance->setDockerClient($client);
        $this->expectException(DockerException::class);

        $instance->stop();
    }

    /**
     * @return void
     */
    public function testDestructSwallowsDockerException()
    {
        $client = (new DockerClient())->withGlobalOptions([
            'host' => 'unix://'.__DIR__.'/missing-docker.sock',
        ]);
        $instance = new GenericContainerInstance([
            'containerId' => new ContainerId('8188d93d8a27'),
        ]);
        $instance->setDockerClient($client);

        $instance = null;

        self::assertNull($instance);
    }
}
