<?php

namespace Tests\Unit\Lifecycle;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Process;
use Testcontainers\Docker\DockerClient;
use Testcontainers\Docker\Exception\DockerException;
use Testcontainers\Docker\Exception\InvalidValueException;
use Testcontainers\Docker\Output\DockerPsOutput;
use Testcontainers\Docker\Types\ContainerListItem;
use Testcontainers\Lifecycle\ContainerReaper;

/**
 * @internal
 *
 * @coversNothing
 */
class ContainerReaperTest extends TestCase
{
    public function testSkipsContainerWithDifferentHostLabel()
    {
        $container = $this->makeContainer([
            'org.testcontainers.host' => 'other-machine',
            'org.testcontainers.pid' => '99999',
        ]);

        $client = $this->createMock(DockerClient::class);
        $client->method('ps')->willReturn($this->makePsOutput([$container]));
        $client->expects($this->never())->method('stop');

        $reaper = new TestableContainerReaper($client);
        $reaper->execute();
    }

    public function testSkipsContainerWithNoHostLabel()
    {
        $container = $this->makeContainer([
            'org.testcontainers.pid' => '99999',
        ]);

        $client = $this->createMock(DockerClient::class);
        $client->method('ps')->willReturn($this->makePsOutput([$container]));
        $client->expects($this->never())->method('stop');

        $reaper = new TestableContainerReaper($client);
        $reaper->execute();
    }

    public function testStopsOrphanedContainerFromSameHost()
    {
        $container = $this->makeContainer([
            'org.testcontainers.host' => (string) gethostname(),
            'org.testcontainers.pid' => '99999',
        ], 'abc123');

        $client = $this->createMock(DockerClient::class);
        $client->method('ps')->willReturn($this->makePsOutput([$container]));
        $client->expects($this->once())->method('stop');

        $reaper = new TestableContainerReaper($client);
        $reaper->setDeadPids([99999]);
        $reaper->execute();
    }

    public function testSkipsContainerOwnedByCurrentProcess()
    {
        $container = $this->makeContainer([
            'org.testcontainers.host' => (string) gethostname(),
            'org.testcontainers.pid' => (string) getmypid(),
        ]);

        $client = $this->createMock(DockerClient::class);
        $client->method('ps')->willReturn($this->makePsOutput([$container]));
        $client->expects($this->never())->method('stop');

        $reaper = new TestableContainerReaper($client);
        $reaper->execute();
    }

    public function testSkipsContainerWithAliveProcess()
    {
        $container = $this->makeContainer([
            'org.testcontainers.host' => (string) gethostname(),
            'org.testcontainers.pid' => '12345',
        ]);

        $client = $this->createMock(DockerClient::class);
        $client->method('ps')->willReturn($this->makePsOutput([$container]));
        $client->expects($this->never())->method('stop');

        $reaper = new TestableContainerReaper($client);
        $reaper->setAlivePids([12345]);
        $reaper->execute();
    }

    public function testBestEffortOnPsException()
    {
        $process = $this->createMock(Process::class);
        $process->method('getCommandLine')->willReturn('docker ps');
        $process->method('getExitCode')->willReturn(1);
        $process->method('getErrorOutput')->willReturn('daemon error');

        $client = $this->createMock(DockerClient::class);
        $client->method('ps')->willThrowException(new DockerException($process));

        $reaper = new TestableContainerReaper($client);

        // Should not throw
        $reaper->execute();
        $this->assertTrue(true);
    }

    public function testBestEffortOnStopException()
    {
        $container = $this->makeContainer([
            'org.testcontainers.host' => (string) gethostname(),
            'org.testcontainers.pid' => '99999',
        ], 'abc123');

        $process = $this->createMock(Process::class);
        $process->method('getCommandLine')->willReturn('docker stop');
        $process->method('getExitCode')->willReturn(1);
        $process->method('getErrorOutput')->willReturn('stop error');

        $client = $this->createMock(DockerClient::class);
        $client->method('ps')->willReturn($this->makePsOutput([$container]));
        $client->method('stop')->willThrowException(new DockerException($process));

        $reaper = new TestableContainerReaper($client);
        $reaper->setDeadPids([99999]);

        // Should not throw
        $reaper->execute();
        $this->assertTrue(true);
    }

    public function testBestEffortOnInvalidValueException()
    {
        $client = $this->createMock(DockerClient::class);
        $client->method('ps')->willThrowException(new InvalidValueException('parse failure'));

        $reaper = new TestableContainerReaper($client);

        // Should not throw
        $reaper->execute();
        $this->assertTrue(true);
    }

    /**
     * @param array<string, string> $labels
     * @param string                $id
     *
     * @return ContainerListItem
     */
    private function makeContainer(array $labels, $id = 'deadbeef')
    {
        $container = $this->createMock(ContainerListItem::class);
        $container->method('getLabel')->willReturnCallback(function ($key) use ($labels) {
            return isset($labels[$key]) ? $labels[$key] : null;
        });
        $container->method('__get')->willReturnCallback(function ($name) use ($id) {
            if ($name === 'id') {
                return $id;
            }

            return null;
        });

        return $container;
    }

    /**
     * @param ContainerListItem[] $containers
     *
     * @return DockerPsOutput
     */
    private function makePsOutput(array $containers)
    {
        $output = $this->createMock(DockerPsOutput::class);
        $output->method('getContainers')->willReturn($containers);

        return $output;
    }
}

/**
 * Testable subclass that overrides isProcessAlive for deterministic tests.
 */
class TestableContainerReaper extends ContainerReaper
{
    /** @var int[] */
    private $alivePids = [];

    /** @var int[] */
    private $deadPids = [];

    /**
     * @param int[] $pids
     */
    public function setAlivePids(array $pids)
    {
        $this->alivePids = $pids;
    }

    /**
     * @param int[] $pids
     */
    public function setDeadPids(array $pids)
    {
        $this->deadPids = $pids;
    }

    protected function isProcessAlive($pid)
    {
        if (in_array($pid, $this->alivePids, true)) {
            return true;
        }

        if (in_array($pid, $this->deadPids, true)) {
            return false;
        }

        return false;
    }
}
