<?php

namespace Tests\Unit\Docker\Command;

use PHPUnit\Framework\TestCase;
use Testcontainers\Docker\Command\RunCommand;
use Testcontainers\Docker\DockerClient;
use Testcontainers\Docker\Exception\PortAlreadyAllocatedException;
use Testcontainers\Docker\Output\DockerRunOutput;
use Testcontainers\Docker\Output\DockerRunWithDetachOutput;
use Testcontainers\Testcontainers;
use Tests\Images\DinD;

/**
 * @internal
 * @coversNothing
 */
class RunCommandTest extends TestCase
{
    public function testHasRunCommandTrait()
    {
        $uses = class_uses(DockerClient::class);

        $this->assertContains(RunCommand::class, $uses);
    }

    public function testRun()
    {
        $client = new DockerClient();
        $output = $client->run('alpine:latest', 'echo', ['Hello, World!']);

        $this->assertInstanceOf(DockerRunOutput::class, $output);
        $this->assertSame(0, $output->getExitCode());
        $this->assertSame("Hello, World!\n", $output->getOutput());
    }

    public function testRunWithDetach()
    {
        $client = new DockerClient();
        $output = $client->run('alpine:latest', 'echo', ['Hello, World!'], [
            'detach' => true,
        ]);

        $this->assertInstanceOf(DockerRunWithDetachOutput::class, $output);
        $this->assertSame(0, $output->getExitCode());
        // @var DockerRunWithDetachOutput $output
        $this->assertNotEmpty($output->getContainerId());
    }

    public function testRunWithTrueOptions()
    {
        $client = new DockerClient();
        $output = $client->run('alpine:latest', 'echo', ['Hello, World!'], [
            'quiet' => true,
        ]);

        $this->assertInstanceOf(DockerRunOutput::class, $output);
        $this->assertSame(0, $output->getExitCode());
        $this->assertSame("Hello, World!\n", $output->getOutput());
    }

    public function testRunWithFalseOptions()
    {
        $client = new DockerClient();
        $output = $client->run('alpine:latest', 'echo', ['Hello, World!'], [
            'detach' => false,
        ]);

        $this->assertInstanceOf(DockerRunOutput::class, $output);
        $this->assertSame(0, $output->getExitCode());
        $this->assertSame("Hello, World!\n", $output->getOutput());
    }

    public function testRunWithArrayOptions()
    {
        $client = new DockerClient();
        $output = $client->run('alpine:latest', 'echo', ['Hello, World!'], [
            'publish' => ['38621:80', '38622:443'],
        ]);

        $this->assertInstanceOf(DockerRunOutput::class, $output);
        $this->assertSame(0, $output->getExitCode());
        $this->assertSame("Hello, World!\n", $output->getOutput());
    }

    public function testRunWithObjectOptions()
    {
        $client = new DockerClient();
        $output = $client->run('alpine:latest', 'printenv', ['BAR'], [
            'env' => [
                'FOO' => 'foo',
                'BAR' => 'bar',
            ],
        ]);

        $this->assertInstanceOf(DockerRunOutput::class, $output);
        $this->assertSame(0, $output->getExitCode());
        $this->assertSame("bar\n", $output->getOutput());
    }

    public function testRunWithPortConflict()
    {
        $this->expectException(PortAlreadyAllocatedException::class);

        $instance = Testcontainers::run(DinD::class);

        $client = new DockerClient();
        $client->withGlobalOptions([
            'host' => 'tcp://'.$instance->getHost().':'.$instance->getMappedPort(2375),
        ]);
        $client->run('alpine:latest', null, ['tail', '-f', '/dev/null'], [
            'detach' => true,
            'publish' => ['38793:80'],
        ]);
        $client->run('alpine:latest', null, ['tail', '-f', '/dev/null'], [
            'detach' => true,
            'publish' => ['38793:80'],
        ]);
    }
}
