<?php

namespace Tests\Unit\Lifecycle;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Process;
use Testcontainers\Docker\DockerClient;
use Testcontainers\Docker\DockerClientFactory;
use Testcontainers\Lifecycle\ContainerReaper;
use Testcontainers\Testcontainers;
use Tests\Images\DinD;

final class ContainerReaperTest extends TestCase
{
    /**
     * @dataProvider providerRetainedContainerLabels
     * @param array<string, string> $labels
     * @return void
     */
    public function testExecuteKeepsContainersWithoutOrphanedOwnership($labels)
    {
        $dind = Testcontainers::run(DinD::class);
        $mappedPort2375 = $dind->getMappedPort(2375);
        $this->assertNotNull($mappedPort2375);
        $client = DockerClientFactory::create([
            'globalOptions' => [
                'host' => 'tcp://'.$dind->getHost().':'.(string) $mappedPort2375,
                'config' => __ROOT__.'/config.json',
            ],
        ]);
        $output = $client->run('alpine:latest', 'sleep', ['300'], [
            'detach' => true,
            'label' => $labels + ['org.testcontainers' => 'true'],
        ]);
        $this->assertInstanceOf(\Testcontainers\Docker\Output\DockerRunWithDetachOutput::class, $output);
        $id = $output->getContainerId();

        try {
            (new ContainerReaper($client))->execute();

            $this->assertSame('running', $client->inspect($id)->state->status);
        } finally {
            $client->rm($id, ['force' => true]);
            $dind->stop();
        }
    }

    /**
     * @return array<string, array{array<string, string>}>
     */
    public static function providerRetainedContainerLabels()
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
        $mappedPort2375 = $dind->getMappedPort(2375);
        $this->assertNotNull($mappedPort2375);
        $client = DockerClientFactory::create([
            'globalOptions' => [
                'host' => 'tcp://'.$dind->getHost().':'.(string) $mappedPort2375,
                'config' => __ROOT__.'/config.json',
            ],
        ]);
        $output = $client->run('alpine:latest', 'sleep', ['300'], [
            'detach' => true,
            'label' => [
                'org.testcontainers' => 'true',
                'org.testcontainers.host' => (string) gethostname(),
                'org.testcontainers.pid' => '2147483647',
            ],
        ]);
        $this->assertInstanceOf(\Testcontainers\Docker\Output\DockerRunWithDetachOutput::class, $output);
        $id = $output->getContainerId();

        try {
            (new ContainerReaper($client))->execute();

            $this->assertSame('exited', $client->inspect($id)->state->status);
        } finally {
            $client->rm($id, ['force' => true]);
            $dind->stop();
        }
    }

    /**
     * @return void
     */
    public function testExecuteKeepsContainerOwnedByAnotherLiveProcess()
    {
        $dind = Testcontainers::run(DinD::class);
        $mappedPort2375 = $dind->getMappedPort(2375);
        $this->assertNotNull($mappedPort2375);
        $client = DockerClientFactory::create([
            'globalOptions' => [
                'host' => 'tcp://'.$dind->getHost().':'.(string) $mappedPort2375,
                'config' => __ROOT__.'/config.json',
            ],
        ]);
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
        $this->assertInstanceOf(\Testcontainers\Docker\Output\DockerRunWithDetachOutput::class, $output);
        $id = $output->getContainerId();

        try {
            (new ContainerReaper($client))->execute();

            $this->assertSame('running', $client->inspect($id)->state->status);
        } finally {
            $client->rm($id, ['force' => true]);
            $dind->stop();
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

        $this->assertNull((new ContainerReaper($client))->execute());
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

        $this->assertNull((new ContainerReaper($client))->execute());
    }
}
