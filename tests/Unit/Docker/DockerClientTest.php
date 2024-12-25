<?php

namespace Tests\Unit\Docker;

use PHPUnit\Framework\TestCase;
use Testcontainers\Docker\DockerClient;
use Testcontainers\Docker\DockerFollowLogsOutput;
use Testcontainers\Docker\DockerLogsOutput;
use Testcontainers\Docker\DockerProcessStatusOutput;
use Testcontainers\Docker\DockerRunOutput;
use Testcontainers\Docker\DockerRunWithDetachOutput;
use Testcontainers\Docker\DockerStopOutput;
use Testcontainers\Docker\Exception\NoSuchContainerException;
use Testcontainers\Docker\Exception\PortAlreadyAllocatedException;
use Testcontainers\Testcontainers;
use Tests\Images\DinD;

class DockerClientTest extends TestCase
{
    public function testRun()
    {
        $client = new DockerClient();
        $output = $client->run('alpine:latest', 'echo', ['Hello, World!']);

        $this->assertInstanceOf(DockerRunOutput::class, $output);
        $this->assertSame(0, $output->getExitCode());
        $this->assertSame("Hello, World!\n", $output->getOutput());
    }

    public function testRunWithDetach()
    {
        $client = new DockerClient();
        $output = $client->run('alpine:latest', 'echo', ['Hello, World!'], [
            'detach' => true,
        ]);

        $this->assertInstanceOf(DockerRunWithDetachOutput::class, $output);
        $this->assertSame(0, $output->getExitCode());
        $this->assertNotEmpty($output->getContainerId());
    }

    public function testRunWithTrueOptions()
    {
        $client = new DockerClient();
        $output = $client->run('alpine:latest', 'echo', ['Hello, World!'], [
            'quiet' => true,
        ]);

        $this->assertInstanceOf(DockerRunOutput::class, $output);
        $this->assertSame(0, $output->getExitCode());
        $this->assertSame("Hello, World!\n", $output->getOutput());
    }

    public function testRunWithFalseOptions()
    {
        $client = new DockerClient();
        $output = $client->run('alpine:latest', 'echo', ['Hello, World!'], [
            'detach' => false,
        ]);

        $this->assertInstanceOf(DockerRunOutput::class, $output);
        $this->assertSame(0, $output->getExitCode());
        $this->assertSame("Hello, World!\n", $output->getOutput());
    }

    public function testRunWithArrayOptions()
    {
        $client = new DockerClient();
        $output = $client->run('alpine:latest', 'echo', ['Hello, World!'], [
            'publish' => ['38621:80', '38622:443'],
        ]);

        $this->assertInstanceOf(DockerRunOutput::class, $output);
        $this->assertSame(0, $output->getExitCode());
        $this->assertSame("Hello, World!\n", $output->getOutput());
    }

    public function testRunWithObjectOptions()
    {
        $client = new DockerClient();
        $output = $client->run('alpine:latest', 'printenv', ['BAR'], [
            'env' => [
                'FOO' => 'foo',
                'BAR' => 'bar',
            ],
        ]);

        $this->assertInstanceOf(DockerRunOutput::class, $output);
        $this->assertSame(0, $output->getExitCode());
        $this->assertSame("bar\n", $output->getOutput());
    }

    public function testRunWithPortConflict()
    {
        $this->expectException(PortAlreadyAllocatedException::class);

        $instance = Testcontainers::run(DinD::class);

        $client = new DockerClient();
        $client->withGlobalOptions([
            'host' => 'tcp://' . $instance->getHost() . ':' . $instance->getMappedPort(2375),
        ]);
        $client->run('nginx:1.27.2', null, null, [
            'detach' => true,
            'publish' => ['38793:80'],
        ]);
        $client->run('nginx:1.27.2', null, null, [
            'detach' => true,
            'publish' => ['38793:80'],
        ]);
    }

    public function testStop()
    {
        $instance = Testcontainers::run(DinD::class);

        $client = new DockerClient();
        $client->withGlobalOptions([
            'host' => 'tcp://' . $instance->getHost() . ':' . $instance->getMappedPort(2375),
        ]);
        $output = $client->run('alpine:latest', 'tail', ['-f', '/dev/null'], [
            'detach' => true,
        ]);
        $containerId = $output->getContainerId();
        $output = $client->stop($containerId);

        $this->assertInstanceOf(DockerStopOutput::class, $output);
        $this->assertSame(0, $output->getExitCode());
        $this->assertSame("$containerId\n", $output->getOutput());
    }

    public function testStopWithNonExistentContainerId()
    {
        $this->expectException(NoSuchContainerException::class);

        $client = new DockerClient();
        $client->stop('aaaaaaaaaaaa');
    }

    public function testProcessStatus()
    {
        $client = new DockerClient();
        $output = $client->run('alpine:latest', 'sleep', ['60'], [
            'detach' => true,
        ]);
        $containerId = $output->getContainerId();
        $output = $client->processStatus();

        $status = $output->get($containerId);

        $this->assertInstanceOf(DockerProcessStatusOutput::class, $output);
        $this->assertSame(0, $output->getExitCode());
        $this->assertSame(substr($containerId, 0, 12), $status['ID']);
        $this->assertSame('alpine:latest', $status['Image']);
        $this->assertSame('running', $status['State']);
    }

    public function testLogs()
    {
        $instance = Testcontainers::run(DinD::class);

        $client = new DockerClient();
        $client->withGlobalOptions([
            'host' => 'tcp://' . $instance->getHost() . ':' . $instance->getMappedPort(2375),
        ]);
        $output = $client->run('jpetazzo/clock:latest', null, null, [
            'detach' => true,
        ]);

        $containerId = $output->getContainerId();
        $logsOutput = $client->logs($containerId);

        $this->assertInstanceOf(DockerLogsOutput::class, $logsOutput);
        $this->assertSame(0, $logsOutput->getExitCode());
        $this->assertNotEmpty($logsOutput->getOutput());
    }

    public function testFollowLogs()
    {
        $instance = Testcontainers::run(DinD::class);

        $client = new DockerClient();
        $client->withGlobalOptions([
            'host' => 'tcp://' . $instance->getHost() . ':' . $instance->getMappedPort(2375),
        ]);
        $output = $client->run('jpetazzo/clock:latest', null, null, [
            'detach' => true,
        ]);

        $containerId = $output->getContainerId();
        $logsOutput = $client->followLogs($containerId);
        $iter = $logsOutput->getIterator();

        $lines = [];
        for ($i = 0; $i < 3; $i++) {
            $lines[] = $iter->current();
            $iter->next();
        }

        $this->assertInstanceOf(DockerFollowLogsOutput::class, $logsOutput);
        $this->assertTrue(preg_match('/\d{2}:\d{2}:\d{2}/', $lines[0]) === 1);
        $this->assertTrue(preg_match('/\d{2}:\d{2}:\d{2}/', $lines[1]) === 1);
        $this->assertTrue(preg_match('/\d{2}:\d{2}:\d{2}/', $lines[2]) === 1);
    }
}
