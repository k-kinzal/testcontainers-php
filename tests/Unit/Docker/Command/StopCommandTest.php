<?php

namespace Tests\Unit\Docker\Command;

use PHPUnit\Framework\TestCase;
use Testcontainers\Docker\Command\StopCommand;
use Testcontainers\Docker\DockerClient;
use Testcontainers\Docker\Exception\NoSuchContainerException;
use Testcontainers\Docker\Output\DockerRunWithDetachOutput;
use Testcontainers\Docker\Output\DockerStopOutput;
use Testcontainers\Testcontainers;
use Tests\Images\DinD;

/**
 * @internal
 * @coversNothing
 */
class StopCommandTest extends TestCase
{
    public function testHasStopCommandTrait()
    {
        $uses = class_uses(DockerClient::class);

        $this->assertContains(StopCommand::class, $uses);
    }

    public function testStop()
    {
        $instance = Testcontainers::run(DinD::class);

        $client = new DockerClient();
        $client->withGlobalOptions([
            'host' => 'tcp://'.$instance->getHost().':'.$instance->getMappedPort(2375),
        ]);
        /** @var DockerRunWithDetachOutput $output */
        $output = $client->run('alpine:latest', 'tail', ['-f', '/dev/null'], [
            'detach' => true,
        ]);
        $containerId = $output->getContainerId();
        $output = $client->stop($containerId);

        $this->assertInstanceOf(DockerStopOutput::class, $output);
        $this->assertSame(0, $output->getExitCode());
        $this->assertSame("{$containerId}\n", $output->getOutput());

        $containerIds = $output->getContainerIds();
        $this->assertCount(1, $containerIds);
        $this->assertSame((string) $containerId, (string) $containerIds[0]);
    }

    public function testStopWithNonExistentContainerId()
    {
        $this->expectException(NoSuchContainerException::class);

        $client = new DockerClient();
        $client->stop('aaaaaaaaaaaa');
    }
}
