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

final class RmCommandTest extends TestCase
{
    /**
     * @return void
     */
    public function testRmIsProvidedByRmCommandTrait()
    {
        $uses = class_uses(DockerClient::class);
        self::assertNotFalse($uses);
        self::assertContains(RmCommand::class, $uses);
    }

    /**
     * @return void
     */
    public function testRm()
    {
        $instance = Testcontainers::run(DinD::class);
        $client = $instance->getData(DockerClient::class);

        /** @var DockerRunWithDetachOutput $runOutput */
        $runOutput = $client->run('alpine:latest', 'echo', ['hello'], [
            'detach' => true,
        ]);
        $containerId = $runOutput->getContainerId();

        // Wait for the container to exit
        usleep(500000);

        $output = $client->rm($containerId);

        self::assertInstanceOf(DockerRmOutput::class, $output);
        self::assertSame(0, $output->getExitCode());

        $containerIds = $output->getContainerIds();
        self::assertCount(1, $containerIds);
        self::assertSame((string) $containerId, (string) $containerIds[0]);
    }

    /**
     * @return void
     */
    public function testRmWithNonExistentContainerId()
    {
        $this->expectException(NoSuchContainerException::class);

        $client = new DockerClient();
        $client->rm('aaaaaaaaaaaa');
    }
}
