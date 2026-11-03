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
    public function testRunIsProvidedByRunCommandTrait()
    {
        $uses = class_uses(DockerClient::class);
        self::assertNotFalse($uses);
        self::assertContains(RunCommand::class, $uses);
    }

    /**
     * @return void
     */
    public function testRun()
    {
        $client = new DockerClient();
        $output = $client->run('alpine:latest', 'echo', ['Hello, World!']);

        self::assertInstanceOf(DockerRunOutput::class, $output);
        self::assertSame(0, $output->getExitCode());
        self::assertSame("Hello, World!\n", $output->getOutput());
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

        self::assertInstanceOf(DockerRunWithDetachOutput::class, $output);
        self::assertSame(0, $output->getExitCode());
        // @var DockerRunWithDetachOutput $output
        self::assertNotEmpty($output->getContainerId());
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

        self::assertInstanceOf(DockerRunOutput::class, $output);
        self::assertSame(0, $output->getExitCode());
        self::assertSame("Hello, World!\n", $output->getOutput());
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

        self::assertInstanceOf(DockerRunOutput::class, $output);
        self::assertSame(0, $output->getExitCode());
        self::assertSame("Hello, World!\n", $output->getOutput());
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

        self::assertInstanceOf(DockerRunOutput::class, $output);
        self::assertSame(0, $output->getExitCode());
        self::assertSame("Hello, World!\n", $output->getOutput());
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

        self::assertInstanceOf(DockerRunOutput::class, $output);
        self::assertSame(0, $output->getExitCode());
        self::assertSame("bar\n", $output->getOutput());
    }

    /**
     * @return void
     */
    public function testRunWithPortConflict()
    {
        $instance = Testcontainers::run(DinD::class);
        $client = $instance->getData(DockerClient::class);
        $output = $client->run('alpine:latest', null, ['tail', '-f', '/dev/null'], [
            'detach' => true,
            'publish' => ['38793:80'],
        ]);
        self::assertInstanceOf(DockerRunWithDetachOutput::class, $output);

        $this->expectException(PortConflictException::class);
        $client->run('alpine:latest', null, ['tail', '-f', '/dev/null'], [
            'detach' => true,
            'publish' => ['38793:80'],
        ]);
    }
}
