<?php

namespace Tests\Unit\Docker;

use PHPUnit\Framework\TestCase;
use Testcontainers\Docker\DockerClient;
use Testcontainers\Docker\Output\DockerFollowLogsOutput;
use Testcontainers\Docker\Output\DockerLogsOutput;
use Testcontainers\Docker\Output\DockerNetworkCreateOutput;
use Testcontainers\Docker\Output\DockerProcessStatusOutput;
use Testcontainers\Testcontainers;
use Tests\Images\DinD;

class DockerClientTest extends TestCase
{
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
        $output = $client->run('jpetazzo/clock:latest', null, [], [
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
        $output = $client->run('jpetazzo/clock:latest', null, [], [
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

    public function testNetworkCreate()
    {
        $instance = Testcontainers::run(DinD::class);

        $client = new DockerClient();
        $client->withGlobalOptions([
            'host' => 'tcp://' . $instance->getHost() . ':' . $instance->getMappedPort(2375),
        ]);
        $output = $client->networkCreate('test-network');

        $this->assertInstanceOf(DockerNetworkCreateOutput::class, $output);
        $this->assertSame(0, $output->getExitCode());
        $this->assertTrue(preg_match('/^[0-9a-f]{64}/', $output->getOutput()) === 1);
    }
}
