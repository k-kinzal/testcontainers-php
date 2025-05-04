<?php

namespace Tests\Unit\Docker\Command;

use PHPUnit\Framework\TestCase;
use Testcontainers\Docker\Command\LogsCommand;
use Testcontainers\Docker\DockerClient;
use Testcontainers\Docker\Output\DockerFollowLogsOutput;
use Testcontainers\Docker\Output\DockerLogsOutput;
use Testcontainers\Docker\Output\DockerRunWithDetachOutput;
use Testcontainers\Testcontainers;
use Tests\Images\DinD;

/**
 * @internal
 *
 * @coversNothing
 */
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
            'host' => 'tcp://'.$instance->getHost().':'.$instance->getMappedPort(2375),
        ]);

        /** @var DockerRunWithDetachOutput $output */
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
            'host' => 'tcp://'.$instance->getHost().':'.$instance->getMappedPort(2375),
        ]);

        /** @var DockerRunWithDetachOutput $output */
        $output = $client->run('jpetazzo/clock:latest', null, [], [
            'detach' => true,
        ]);

        $containerId = $output->getContainerId();
        $logsOutput = $client->logs($containerId, [
            'follow' => true,
        ]);

        $this->assertInstanceOf(DockerFollowLogsOutput::class, $logsOutput);

        /** @var DockerFollowLogsOutput $logsOutput */
        $iter = $logsOutput->getIterator();
        $lines = [];
        for ($i = 0; $i < 3; ++$i) {
            $lines[] = $iter->current();
            $iter->next();
        }

        $this->assertTrue(1 === preg_match('/\d{2}:\d{2}:\d{2}/', $lines[0]));
        $this->assertTrue(1 === preg_match('/\d{2}:\d{2}:\d{2}/', $lines[1]));
        $this->assertTrue(1 === preg_match('/\d{2}:\d{2}:\d{2}/', $lines[2]));
    }
}
