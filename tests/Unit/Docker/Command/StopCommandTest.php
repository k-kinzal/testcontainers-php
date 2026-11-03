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

final class StopCommandTest extends TestCase
{
    /**
     * @return void
     */
    public function testStopIsProvidedByStopCommandTrait()
    {
        $uses = class_uses(DockerClient::class);
        self::assertNotFalse($uses);
        self::assertContains(StopCommand::class, $uses);
    }

    /**
     * @return void
     */
    public function testStop()
    {
        $instance = Testcontainers::run(DinD::class);
        $mappedPort2375 = $instance->getMappedPort(2375);
        self::assertNotNull($mappedPort2375);

        $client = new DockerClient();
        $client->withGlobalOptions([
            'host' => 'tcp://'.$instance->getHost().':'.(string) $mappedPort2375,
            'config' => __ROOT__.'/config.json',
        ]);

        /** @var DockerRunWithDetachOutput $output */
        $output = $client->run('alpine:latest', 'tail', ['-f', '/dev/null'], [
            'detach' => true,
        ]);
        $containerId = $output->getContainerId();
        $output = $client->stop($containerId);

        self::assertInstanceOf(DockerStopOutput::class, $output);
        self::assertSame(0, $output->getExitCode());
        self::assertSame("{$containerId}\n", $output->getOutput());

        $containerIds = $output->getContainerIds();
        self::assertCount(1, $containerIds);
        self::assertSame((string) $containerId, (string) $containerIds[0]);
    }

    /**
     * @return void
     */
    public function testStopWithNonExistentContainerId()
    {
        $this->expectException(NoSuchContainerException::class);

        $client = new DockerClient();
        $client->stop('aaaaaaaaaaaa');
    }
}
