<?php

namespace Tests\Unit\Docker\Command;

use PHPUnit\Framework\TestCase;
use Testcontainers\Docker\Command\PsCommand;
use Testcontainers\Docker\DockerClient;
use Testcontainers\Docker\Output\DockerPsOutput;
use Testcontainers\Docker\Output\DockerRunWithDetachOutput;
use Testcontainers\Testcontainers;
use Tests\Images\DinD;

final class PsCommandTest extends TestCase
{
    /**
     * @return void
     */
    public function testPsIsProvidedByPsCommandTrait()
    {
        $uses = class_uses(DockerClient::class);
        self::assertNotFalse($uses);
        self::assertContains(PsCommand::class, $uses);
    }

    /**
     * @return void
     */
    public function testPs()
    {
        $instance = Testcontainers::run(DinD::class);
        $client = $instance->getData(DockerClient::class);

        /** @var DockerRunWithDetachOutput $runOutput */
        $runOutput = $client->run('alpine:latest', 'tail', ['-f', '/dev/null'], [
            'detach' => true,
            'label' => ['test.ps=true'],
        ]);

        $output = $client->ps([
            'filter' => ['label=test.ps=true'],
        ]);

        self::assertInstanceOf(DockerPsOutput::class, $output);

        $containers = $output->getContainers();
        self::assertCount(1, $containers);
        self::assertSame((string) $runOutput->getContainerId(), (string) $containers[0]->id);

        $client->stop($runOutput->getContainerId(), ['timeout' => 0]);
    }

    /**
     * @return void
     */
    public function testPsReturnsEmptyWhenNoContainersMatch()
    {
        $client = new DockerClient();
        $output = $client->ps([
            'filter' => ['label=test.nonexistent.label.xyz=1'],
        ]);

        self::assertInstanceOf(DockerPsOutput::class, $output);
        self::assertCount(0, $output->getContainers());
    }
}
