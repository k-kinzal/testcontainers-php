<?php

namespace Tests\Unit\Docker\Command;

use PHPUnit\Framework\TestCase;
use Testcontainers\Docker\Command\RunCommand;
use Testcontainers\Docker\DockerClient;
use Testcontainers\Docker\Exception\PortConflictException;
use Testcontainers\Docker\Output\DockerRunOutput;
use Testcontainers\Docker\Output\DockerRunWithDetachOutput;
use Testcontainers\Testcontainers;
use Tests\Images\DinD;

final class RunCommandTest extends TestCase
{
    /**
     * @return void
     */
    public function testHasRunCommandTrait()
    {
        $uses = class_uses(DockerClient::class);

        $this->assertContains(RunCommand::class, $uses);
    }

    /**
     * @return void
     */
    public function testRun()
    {
        $client = new DockerClient();
        $output = $client->run('alpine:latest', 'echo', ['Hello, World!']);

        $this->assertInstanceOf(DockerRunOutput::class, $output);
        $this->assertSame(0, $output->getExitCode());
        $this->assertSame("Hello, World!\n", $output->getOutput());
    }

    /**
     * @return void
     */
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

    /**
     * @return void
     */
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

    /**
     * @return void
     */
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

    /**
     * @return void
     */
    public function testRunWithArrayOptions()
    {
        $client = new DockerClient();
        $output = $client->run('alpine:latest', 'echo', ['Hello, World!'], [
            'label' => ['test.label1=value1', 'test.label2=value2'],
        ]);

        $this->assertInstanceOf(DockerRunOutput::class, $output);
        $this->assertSame(0, $output->getExitCode());
        $this->assertSame("Hello, World!\n", $output->getOutput());
    }

    /**
     * @return void
     */
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

    /**
     * @return void
     */
    public function testRunWithPortConflict()
    {
        $this->expectException(PortConflictException::class);

        $instance = Testcontainers::run(DinD::class);

        $client = new DockerClient();
        $client->withGlobalOptions([
            'host' => 'tcp://'.$instance->getHost().':'.$instance->getMappedPort(2375),
            'config' => __ROOT__.'/config.json',
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
