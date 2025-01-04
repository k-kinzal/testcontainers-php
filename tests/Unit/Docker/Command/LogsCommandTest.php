<?php

namespace Tests\Unit\Docker\Command;

use PHPUnit\Framework\TestCase;
use Testcontainers\Docker\Command\LogsCommand;
use Testcontainers\Docker\DockerClient;
use Testcontainers\Docker\Output\DockerFollowLogsOutput;
use Testcontainers\Docker\Output\DockerLogsOutput;
use Testcontainers\Testcontainers;
use Tests\Images\DinD;

class LogsCommandTest extends TestCase
{
    public function testHasLogsCommandTrait()
    {
        $uses = class_uses(DockerClient::class);

        $this->assertContains(LogsCommand::class, $uses);
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

    public function testLogsWithFollow()
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
        $logsOutput = $client->logs($containerId, [
            'follow' => true,
        ]);
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
