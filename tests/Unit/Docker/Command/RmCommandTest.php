<?php

namespace Tests\Unit\Docker\Command;

use PHPUnit\Framework\TestCase;
use Testcontainers\Docker\Command\RmCommand;
use Testcontainers\Docker\DockerClient;
use Testcontainers\Docker\Exception\NoSuchContainerException;
use Testcontainers\Docker\Output\DockerRmOutput;
use Testcontainers\Docker\Output\DockerRunWithDetachOutput;
use Testcontainers\Testcontainers;
use Tests\Images\DinD;

/**
 * @internal
 *
 * @coversNothing
 */
class RmCommandTest extends TestCase
{
    public function testHasRmCommandTrait()
    {
        $uses = class_uses(DockerClient::class);

        $this->assertContains(RmCommand::class, $uses);
    }

    public function testRm()
    {
        $instance = Testcontainers::run(DinD::class);

        $client = new DockerClient();
        $client->withGlobalOptions([
            'host' => 'tcp://'.$instance->getHost().':'.$instance->getMappedPort(2375),
            'config' => __ROOT__.'/config.json',
        ]);

        /** @var DockerRunWithDetachOutput $runOutput */
        $runOutput = $client->run('alpine:latest', 'echo', ['hello'], [
            'detach' => true,
        ]);
        $containerId = $runOutput->getContainerId();

        // Wait for the container to exit
        usleep(500000);

        $output = $client->rm($containerId);

        $this->assertInstanceOf(DockerRmOutput::class, $output);
        $this->assertSame(0, $output->getExitCode());

        $containerIds = $output->getContainerIds();
        $this->assertCount(1, $containerIds);
        $this->assertSame((string) $containerId, (string) $containerIds[0]);
    }

    public function testRmWithNonExistentContainerId()
    {
        $this->expectException(NoSuchContainerException::class);

        $client = new DockerClient();
        $client->rm('aaaaaaaaaaaa');
    }
}
