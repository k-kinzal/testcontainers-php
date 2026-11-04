<?php

namespace Tests\Unit\Docker\Command;

use PHPUnit\Framework\TestCase;
use Testcontainers\Docker\Command\LogsCommand;
use Testcontainers\Docker\DockerClient;
use Testcontainers\Docker\Output\DockerFollowLogsOutput;
use Testcontainers\Docker\Output\DockerLogsOutput;
use Testcontainers\Docker\Output\DockerRunWithDetachOutput;
use Testcontainers\Testcontainers;
use Tests\Images\DinD;

final class LogsCommandTest extends TestCase
{
    /**
     * @return void
     */
    public function testLogsIsProvidedByLogsCommandTrait()
    {
        $uses = class_uses(DockerClient::class);
        self::assertNotFalse($uses);
        self::assertContains(LogsCommand::class, $uses);
    }

    /**
     * @return void
     */
    public function testLogs()
    {
        $instance = Testcontainers::run(DinD::class);
        $client = $instance->getData(DockerClient::class);

        /** @var DockerRunWithDetachOutput $output */
        $output = $client->run('jpetazzo/clock:latest', null, [], [
            'rm' => true,
            'detach' => true,
        ]);

        $containerId = $output->getContainerId();
        $logsOutput = $client->logs($containerId);

        self::assertInstanceOf(DockerLogsOutput::class, $logsOutput);
        self::assertSame(0, $logsOutput->getExitCode());
        self::assertNotEmpty($logsOutput->getOutput());
    }

    /**
     * @return void
     */
    public function testLogsWithFollow()
    {
        $instance = Testcontainers::run(DinD::class);
        $client = $instance->getData(DockerClient::class);

        /** @var DockerRunWithDetachOutput $output */
        $output = $client->run('jpetazzo/clock:latest', null, [], [
            'rm' => true,
            'detach' => true,
        ]);

        $containerId = $output->getContainerId();
        $logsOutput = $client->logs($containerId, [
            'follow' => true,
        ]);

        self::assertInstanceOf(DockerFollowLogsOutput::class, $logsOutput);

        $iter = $logsOutput->getIterator();
        $lines = [];
        $lines[] = $iter->current();
        $iter->next();
        $lines[] = $iter->current();
        $iter->next();
        $lines[] = $iter->current();

        self::assertNotNull($lines[0]);
        self::assertTrue(preg_match('/\d{2}:\d{2}:\d{2}/', $lines[0]) === 1);
        self::assertNotNull($lines[1]);
        self::assertTrue(preg_match('/\d{2}:\d{2}:\d{2}/', $lines[1]) === 1);
        self::assertNotNull($lines[2]);
        self::assertTrue(preg_match('/\d{2}:\d{2}:\d{2}/', $lines[2]) === 1);
    }
}
