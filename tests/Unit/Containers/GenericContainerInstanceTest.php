<?php

/** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Unit\Containers;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Process;
use Testcontainers\Containers\GenericContainer\GenericContainer;
use Testcontainers\Containers\GenericContainer\GenericContainerInstance;
use Testcontainers\Containers\Types\ImagePullPolicy;
use Testcontainers\Docker\DockerClient;
use Testcontainers\Docker\DockerClientFactory;
use Testcontainers\Docker\Exception\DockerException;
use Testcontainers\Docker\Output\DockerEventsOutput;
use Testcontainers\Docker\Output\DockerInspectOutput;
use Testcontainers\Docker\Output\DockerRunWithDetachOutput;
use Testcontainers\Docker\Types\ContainerId;
use Testcontainers\SSH\Session;
use Testcontainers\Testcontainers;
use Tests\Images\DinD;

/**
 * @internal
 *
 * @coversNothing
 */
class GenericContainerInstanceTest extends TestCase
{
    public function testGetContainerId()
    {
        $instance = new GenericContainerInstance([
            'containerId' => new ContainerId('8188d93d8a27'),
        ]);

        $this->assertSame('8188d93d8a27', (string) $instance->getContainerId());
    }

    public function testGetLabel()
    {
        $instance = new GenericContainerInstance([
            'containerId' => new ContainerId('8188d93d8a27'),
            'labels' => [
                'com.example.label' => 'value',
            ],
        ]);

        $this->assertSame('value', $instance->getLabel('com.example.label'));
    }

    public function testGetLabels()
    {
        $instance = new GenericContainerInstance([
            'containerId' => new ContainerId('8188d93d8a27'),
            'labels' => [
                'com.example.label' => 'value',
            ],
        ]);

        $this->assertSame(['com.example.label' => 'value'], $instance->getLabels());
    }

    public function testGetHost()
    {
        $instance = new GenericContainerInstance([
            'containerId' => new ContainerId('8188d93d8a27'),
        ]);

        $this->assertSame('localhost', $instance->getHost());
    }

    public function testGetHostFromDockerHost()
    {
        $dind = Testcontainers::run(DinD::class);
        $client = DockerClientFactory::create([
            'globalOptions' => [
                'host' => 'tcp://'.$dind->getHost().':'.$dind->getMappedPort(2375),
                'config' => __ROOT__.'/config.json',
            ],
        ]);

        $instance = new GenericContainerInstance([
            'containerId' => new ContainerId('8188d93d8a27'),
        ]);
        $instance->setDockerClient($client);

        $this->assertSame($dind->getHost(), $instance->getHost());
    }

    public function testGetHostFromPortForward()
    {
        $dind = Testcontainers::run(DinD::class);
        $client = DockerClientFactory::create([
            'globalOptions' => [
                'host' => 'tcp://'.$dind->getHost().':'.$dind->getMappedPort(2375),
                'config' => __ROOT__.'/config.json',
            ],
        ]);

        $instance = new GenericContainerInstance([
            'containerId' => new ContainerId('8188d93d8a27'),
        ]);
        $instance->setDockerClient($client);
        $instance->setData(new Session(new Process(['pwd'])));

        $this->assertSame('localhost', $instance->getHost());
    }

    public function testGetHostFromOverride()
    {
        try {
            putenv('TESTCONTAINERS_HOST_OVERRIDE=override.local');
            $instance = new GenericContainerInstance([
                'containerId' => new ContainerId('8188d93d8a27'),
            ]);

            $this->assertSame('override.local', $instance->getHost());
        } finally {
            putenv('TESTCONTAINERS_HOST_OVERRIDE');
        }
    }

    public function testGetMappedPort()
    {
        $instance = new GenericContainerInstance([
            'containerId' => new ContainerId('8188d93d8a27'),
            'ports' => [
                80 => 8080,
            ],
        ]);

        $this->assertSame(8080, $instance->getMappedPort(80));
    }

    public function testGetImagePullPolicy()
    {
        $pullPolicy = ImagePullPolicy::ALWAYS();
        $instance = new GenericContainerInstance([
            'containerId' => new ContainerId('8188d93d8a27'),
            'pull' => $pullPolicy,
        ]);

        $this->assertSame($pullPolicy, $instance->getImagePullPolicy());
    }

    public function testGetPrivilegedMode()
    {
        $instance = new GenericContainerInstance([
            'containerId' => new ContainerId('8188d93d8a27'),
            'privileged' => true,
        ]);

        $this->assertTrue($instance->getPrivilegedMode());
    }

    public function testGetOutput()
    {
        $container = (new GenericContainer('alpine:latest'))
            ->withAutoRemoveOnExit(false)
            ->withCommands(['echo', 'Hello, World!'])
        ;
        $instance = $container->start();

        while ($instance->isRunning()) {
            usleep(100);
        }

        $this->assertSame("Hello, World!\n", $instance->getOutput());
    }

    public function testGetErrorOutput()
    {
        $container = (new GenericContainer('alpine:latest'))
            ->withAutoRemoveOnExit(false)
            ->withCommands(['ls', '/not-exist-dir'])
        ;
        $instance = $container->start();

        while ($instance->isRunning()) {
            usleep(100);
        }

        $this->assertSame("ls: /not-exist-dir: No such file or directory\n", $instance->getErrorOutput());
    }

    public function testSetDataAndGetData()
    {
        $data = new CustomData();

        $instance = new GenericContainerInstance([
            'containerId' => new ContainerId('8188d93d8a27'),
        ]);
        $instance->setData($data);

        $this->assertSame($data, $instance->getData(CustomData::class));
    }

    public function testIsRunning()
    {
        $instance = new GenericContainerInstance([
            'containerId' => new ContainerId('8188d93d8a27'),
        ]);

        $this->assertFalse($instance->isRunning());
    }

    public function testIsRunningReusesInspectedStateUntilEventIsEmitted()
    {
        $status = 'running';
        $count = 0;
        $instance = new GenericContainerInstance([
            'containerId' => new ContainerId('8188d93d8a27'),
        ]);
        $instance->setDockerClient($this->createDockerClient($status, $count, ['create', 'die']));

        $deadline = time() + 10;
        do {
            $inspected = $count;
            $this->assertTrue($instance->isRunning());
            usleep(10000);
        } while ($count !== $inspected && time() < $deadline);

        for ($i = 0; $i < 100; ++$i) {
            $this->assertTrue($instance->isRunning());
        }
        $this->assertSame($inspected, $count);

        $status = 'exited';
        $deadline = time() + 5;
        while ($instance->isRunning() && time() < $deadline) {
            usleep(10000);
        }
        $this->assertFalse($instance->isRunning());
        $this->assertTrue($instance->isStopped());
        $this->assertGreaterThan($inspected, $count);
    }

    public function testIsRunningInspectsWithoutEvents()
    {
        $status = 'running';
        $count = 0;
        $instance = new GenericContainerInstance([
            'containerId' => new ContainerId('8188d93d8a27'),
        ]);
        $instance->setDockerClient($this->createDockerClient($status, $count, []));

        $this->assertTrue($instance->isRunning());
        $this->assertTrue($instance->isRunning());
        $this->assertSame(2, $count);
    }

    public function testIsRunningInspectsAfterInspectIntervalElapses()
    {
        $status = 'running';
        $count = 0;
        $instance = new GenericContainerInstance([
            'containerId' => new ContainerId('8188d93d8a27'),
            'inspectInterval' => 0.5,
        ]);
        $instance->setDockerClient($this->createDockerClient($status, $count, ['create']));

        $deadline = time() + 10;
        do {
            $inspected = $count;
            $this->assertTrue($instance->isRunning());
            usleep(10000);
        } while ($count !== $inspected && time() < $deadline);

        usleep(500000);
        $this->assertTrue($instance->isRunning());
        $this->assertSame($inspected + 1, $count);
    }

    public function testIsRunningInspectsEveryTimeWithInspectIntervalOfZero()
    {
        $status = 'running';
        $count = 0;
        $instance = new GenericContainerInstance([
            'containerId' => new ContainerId('8188d93d8a27'),
            'inspectInterval' => 0,
        ]);
        $instance->setDockerClient($this->createDockerClient($status, $count, ['create']));

        $deadline = microtime(true) + 0.3;
        while (microtime(true) < $deadline) {
            $this->assertTrue($instance->isRunning());
            usleep(10000);
        }
        $inspected = $count;

        $this->assertTrue($instance->isRunning());
        $this->assertTrue($instance->isRunning());
        $this->assertSame($inspected + 2, $count);
    }

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

        $this->assertFalse($instance->isStopped());

        $instance->stop();

        $this->assertTrue($instance->isStopped());

        $client->rm($output->getContainerId());

        $this->assertTrue($instance->isStopped());
    }

    public function testGetState()
    {
        $status = 'running';
        $count = 0;
        $instance = new GenericContainerInstance([
            'containerId' => new ContainerId('8188d93d8a27'),
        ]);
        $instance->setDockerClient($this->createDockerClient($status, $count, []));

        $this->assertSame('running', $instance->getState()->status);
    }

    public function testGetStateIfContainerDoesNotExist()
    {
        $instance = new GenericContainerInstance([
            'containerId' => new ContainerId('8188d93d8a27'),
        ]);

        $this->assertNull($instance->getState());
    }

    public function testIsStoppedIfContainerDoesNotExist()
    {
        $instance = new GenericContainerInstance([
            'containerId' => new ContainerId('8188d93d8a27'),
        ]);

        $this->assertTrue($instance->isStopped());
    }

    public function testGetStateSubscribesEventsOfContainer()
    {
        $options = null;
        $client = $this->createMock(DockerClient::class);
        $client->expects($this->once())->method('events')->willReturnCallback(function ($arg) use (&$options) {
            $options = $arg;

            return self::createEventsOutput([]);
        });
        $client->method('inspect')->willReturn(self::createInspectOutput('running'));
        $instance = new GenericContainerInstance([
            'containerId' => new ContainerId('8188d93d8a27'),
        ]);
        $instance->setDockerClient($client);

        $instance->getState();
        $instance->getState();

        $this->assertContains('container=8188d93d8a27', $options['filter']);
        $this->assertContains('event=die', $options['filter']);
        $this->assertSame('0', $options['since']);
        $this->assertNull($options['timeout']);
    }

    public function testStopStopsObservingEvents()
    {
        $process = new Process(['sleep', '30']);
        $process->start();
        $client = $this->createMock(DockerClient::class);
        $client->expects($this->once())->method('events')->willReturnCallback(function () use ($process) {
            return new DockerEventsOutput($process, true);
        });
        $client->method('inspect')->willReturn(self::createInspectOutput('running'));
        $instance = new GenericContainerInstance([
            'containerId' => new ContainerId('8188d93d8a27'),
        ]);
        $instance->setDockerClient($client);

        $this->assertTrue($instance->isRunning());
        $this->assertTrue($process->isRunning());

        $instance->stop();

        $this->assertFalse($process->isRunning());
    }

    public function testStop()
    {
        $instance = new GenericContainerInstance([
            'containerId' => new ContainerId('8188d93d8a27'),
        ]);
        $instance->stop();

        $this->assertFalse($instance->isRunning());
    }

    public function testStopThrowsDockerException()
    {
        $process = $this->createMock(Process::class);
        $process->method('getCommandLine')->willReturn('docker stop test');
        $process->method('getExitCode')->willReturn(1);
        $process->method('getErrorOutput')->willReturn('connection refused');

        $client = $this->createMock(DockerClient::class);
        $client->method('stop')->willThrowException(new DockerException($process));

        $instance = new GenericContainerInstance([
            'containerId' => new ContainerId('8188d93d8a27'),
        ]);
        $instance->setDockerClient($client);

        $this->expectException(DockerException::class);
        $instance->stop();
    }

    public function testDestructorSwallowsDockerException()
    {
        $process = $this->createMock(Process::class);
        $process->method('getCommandLine')->willReturn('docker stop test');
        $process->method('getExitCode')->willReturn(1);
        $process->method('getErrorOutput')->willReturn('connection refused');

        $client = $this->createMock(DockerClient::class);
        $client->method('stop')->willThrowException(new DockerException($process));

        $instance = new GenericContainerInstance([
            'containerId' => new ContainerId('8188d93d8a27'),
        ]);
        $instance->setDockerClient($client);

        // Should not throw -- destructor catches all exceptions
        unset($instance);

        $this->assertTrue(true);
    }

    /**
     * @param string   $status
     * @param int      $count
     * @param string[] $actions
     *
     * @return DockerClient
     */
    private function createDockerClient(&$status, &$count, $actions)
    {
        $outputs = [
            'running' => self::createInspectOutput('running'),
            'exited' => self::createInspectOutput('exited'),
        ];
        $client = $this->createMock(DockerClient::class);
        $client->expects($this->atLeastOnce())->method('inspect')->willReturnCallback(function () use (&$status, &$count, $outputs) {
            ++$count;

            return $outputs[$status];
        });
        $client->method('events')->willReturnCallback(function () use ($actions) {
            return self::createEventsOutput($actions);
        });

        return $client;
    }

    /**
     * @param string $status
     *
     * @return DockerInspectOutput
     */
    private static function createInspectOutput($status)
    {
        $output = json_encode([['State' => ['Status' => $status, 'ExitCode' => 0]]]);
        $process = new Process([PHP_BINARY, '-r', 'echo '.var_export($output, true).';']);
        $process->run();

        return new DockerInspectOutput($process);
    }

    /**
     * @param string[] $actions
     *
     * @return DockerEventsOutput
     */
    private static function createEventsOutput($actions)
    {
        $script = '';
        foreach ($actions as $i => $action) {
            $event = json_encode([
                'Type' => 'container',
                'Action' => $action,
                'Actor' => ['ID' => '8188d93d8a27', 'Attributes' => null],
                'scope' => 'local',
                'time' => 1700000000 + $i,
                'timeNano' => (1700000000 + $i) * 1000000000,
            ]);
            $script .= 'echo '.var_export($event."\n", true).'; flush(); usleep(2000000);';
        }
        $process = new Process([PHP_BINARY, '-r', $script.'sleep(30);']);
        $process->start();

        return new DockerEventsOutput($process, true);
    }
}

class CustomData
{
}
