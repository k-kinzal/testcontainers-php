<?php

namespace Tests\Unit\Docker\Command;

use PHPUnit\Framework\TestCase;
use Testcontainers\Docker\Command\InspectCommand;
use Testcontainers\Docker\DockerClient;
use Testcontainers\Docker\Output\DockerInspectOutput;
use Testcontainers\Docker\Output\DockerRunWithDetachOutput;

/**
 * @internal
 *
 * @coversNothing
 */
class InspectCommandTest extends TestCase
{
    public function testHasInspectCommandTrait()
    {
        $uses = class_uses(DockerClient::class);

        $this->assertContains(InspectCommand::class, $uses);
    }

    public function testInspect()
    {
        $client = new DockerClient();

        /** @var DockerRunWithDetachOutput $output */
        $output = $client->run('alpine:latest', 'echo', ['Hello, World!'], [
            'detach' => true,
        ]);
        $containerId = $output->getContainerId();
        $output = $client->inspect($containerId);

        $this->assertInstanceOf(DockerInspectOutput::class, $output);
        $this->assertSame(0, $output->getExitCode());
        $this->assertContains($output->state->status, [
            'created',
            'running',
            'paused',
            'restarting',
            'removing',
            'exited',
            'dead',
        ]);
        $this->assertSame(0, $output->state->exitCode);
    }
}
