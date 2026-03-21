<?php

namespace Tests\Unit\Docker\Command;

use PHPUnit\Framework\TestCase;
use Testcontainers\Docker\Command\PsCommand;
use Testcontainers\Docker\DockerClient;
use Testcontainers\Docker\Output\DockerPsOutput;
use Testcontainers\Docker\Output\DockerRunWithDetachOutput;
use Testcontainers\Testcontainers;
use Tests\Images\DinD;

/**
 * @internal
 *
 * @coversNothing
 */
class PsCommandTest extends TestCase
{
    public function testHasPsCommandTrait()
    {
        $uses = class_uses(DockerClient::class);

        $this->assertContains(PsCommand::class, $uses);
    }

    public function testPs()
    {
        $instance = Testcontainers::run(DinD::class);

        $client = new DockerClient();
        $client->withGlobalOptions([
            'host' => 'tcp://'.$instance->getHost().':'.$instance->getMappedPort(2375),
            'config' => __ROOT__.'/config.json',
        ]);

        /** @var DockerRunWithDetachOutput $runOutput */
        $runOutput = $client->run('alpine:latest', 'tail', ['-f', '/dev/null'], [
            'detach' => true,
            'label' => ['test.ps=true'],
        ]);

        $output = $client->ps([
            'filter' => ['label=test.ps=true'],
        ]);

        $this->assertInstanceOf(DockerPsOutput::class, $output);

        $containers = $output->getContainers();
        $this->assertCount(1, $containers);
        $this->assertSame((string) $runOutput->getContainerId(), (string) $containers[0]->id);

        $client->stop($runOutput->getContainerId());
    }

    public function testPsReturnsEmptyWhenNoContainersMatch()
    {
        $client = new DockerClient();
        $output = $client->ps([
            'filter' => ['label=test.nonexistent.label.xyz=1'],
        ]);

        $this->assertInstanceOf(DockerPsOutput::class, $output);
        $this->assertCount(0, $output->getContainers());
    }
}
