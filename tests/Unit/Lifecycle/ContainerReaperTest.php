<?php

namespace Tests\Unit\Lifecycle;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Process;
use Testcontainers\Docker\DockerClient;
use Testcontainers\Lifecycle\ContainerReaper;
use Testcontainers\Testcontainers;
use Tests\Images\DinD;

final class ContainerReaperTest extends TestCase
{
    /**
     * @dataProvider providerExecuteKeepsContainersWithoutOrphanedOwnership
     * @param array<string, string> $labels
     * @return void
     */
    public function testExecuteKeepsContainersWithoutOrphanedOwnership($labels)
    {
        $dind = Testcontainers::run(DinD::class);
        $client = $dind->getData(DockerClient::class);
        $output = $client->run('alpine:latest', 'sleep', ['300'], [
            'detach' => true,
            'label' => $labels + ['org.testcontainers' => 'true'],
        ]);
        self::assertInstanceOf(\Testcontainers\Docker\Output\DockerRunWithDetachOutput::class, $output);
        $id = $output->getContainerId();

        try {
            (new ContainerReaper($client))->execute();

            self::assertSame('running', $client->inspect($id)->state->status);
        } finally {
            $client->rm($id, ['force' => true]);
        }
    }

    /**
     * @return array<string, array{array<string, string>}>
     */
    public static function providerExecuteKeepsContainersWithoutOrphanedOwnership()
    {
        return [
            'different host' => [[
                'org.testcontainers.host' => 'other-machine',
                'org.testcontainers.pid' => '2147483647',
            ]],
            'missing host' => [['org.testcontainers.pid' => '2147483647']],
            'current process' => [[
                'org.testcontainers.host' => (string) gethostname(),
                'org.testcontainers.pid' => (string) getmypid(),
            ]],
            'missing pid' => [['org.testcontainers.host' => (string) gethostname()]],
            'empty pid' => [[
                'org.testcontainers.host' => (string) gethostname(),
                'org.testcontainers.pid' => '',
            ]],
        ];
    }

    /**
     * @return void
     */
    public function testExecuteStopsOrphanedContainerFromSameHost()
    {
        $dind = Testcontainers::run(DinD::class);
        $client = $dind->getData(DockerClient::class);
        $output = $client->run('alpine:latest', 'sleep', ['300'], [
            'detach' => true,
            'label' => [
                'org.testcontainers' => 'true',
                'org.testcontainers.host' => (string) gethostname(),
                'org.testcontainers.pid' => '2147483647',
            ],
        ]);
        self::assertInstanceOf(\Testcontainers\Docker\Output\DockerRunWithDetachOutput::class, $output);
        $id = $output->getContainerId();

        try {
            (new ContainerReaper($client))->execute();

            self::assertSame('exited', $client->inspect($id)->state->status);
        } finally {
            $client->rm($id, ['force' => true]);
        }
    }

    /**
     * @return void
     */
    public function testExecuteKeepsContainerOwnedByAnotherLiveProcess()
    {
        $dind = Testcontainers::run(DinD::class);
        $client = $dind->getData(DockerClient::class);
        $process = new Process(['sleep', '300']);
        $process->start();
        $output = $client->run('alpine:latest', 'sleep', ['300'], [
            'detach' => true,
            'label' => [
                'org.testcontainers' => 'true',
                'org.testcontainers.host' => (string) gethostname(),
                'org.testcontainers.pid' => (string) $process->getPid(),
            ],
        ]);
        self::assertInstanceOf(\Testcontainers\Docker\Output\DockerRunWithDetachOutput::class, $output);
        $id = $output->getContainerId();

        try {
            (new ContainerReaper($client))->execute();

            self::assertSame('running', $client->inspect($id)->state->status);
        } finally {
            $client->rm($id, ['force' => true]);
            $process->stop();
        }
    }

    /**
     * @return void
     */
    public function testExecuteHandlesUnavailableDockerDaemon()
    {
        $client = (new DockerClient())->withGlobalOptions([
            'host' => 'unix://'.__DIR__.'/missing-docker.sock',
        ]);

        self::assertNull((new ContainerReaper($client))->execute());
    }

    /**
     * @return void
     */
    public function testExecuteHandlesInvalidCommandOutput()
    {
        $client = (new DockerClient())
            ->withCommand(PHP_BINARY)
            ->withGlobalOptions(['version' => true])
        ;

        self::assertNull((new ContainerReaper($client))->execute());
    }
}
