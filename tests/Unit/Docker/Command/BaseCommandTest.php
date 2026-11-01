<?php

namespace Tests\Unit\Docker\Command;

use PHPUnit\Framework\TestCase;
use Testcontainers\Docker\Command\BaseCommand;
use Testcontainers\Docker\DockerClient;

/**
 * @internal
 *
 * @coversNothing
 */
class BaseCommandTest extends TestCase
{
    public function testExecutesWithProcessOptions()
    {
        $command = (new BaseCommandHarness())
            ->withCommand(PHP_BINARY)
            ->withProcOptions(['blocking_pipes' => false])
        ;

        $process = $command->execute('-r', null, ['echo "options-ok";']);

        $this->assertSame('options-ok', $process->getOutput());
    }

    public function testGetHostFromDefault()
    {
        $backup = getenv('DOCKER_HOST');

        try {
            putenv('DOCKER_HOST');
            $client = new DockerClient();

            $this->assertSame('unix:///var/run/docker.sock', $client->getHost());
        } finally {
            putenv('DOCKER_HOST='.$backup);
        }
    }

    public function testGetHostFromOptions()
    {
        $client = (new DockerClient())
            ->withGlobalOptions([
                'host' => 'tcp://127.0.0.1:2375',
            ])
        ;

        $this->assertSame('tcp://127.0.0.1:2375', $client->getHost());
    }

    public function testGetHostFromEnv()
    {
        $client = (new DockerClient())
            ->withEnv([
                'DOCKER_HOST' => 'tcp://127.0.0.1:2375',
            ])
        ;

        $this->assertSame('tcp://127.0.0.1:2375', $client->getHost());
    }

    public function testGetHostFromGlobalEnv()
    {
        $backup = getenv('DOCKER_HOST');

        try {
            putenv('DOCKER_HOST=tcp://127.0.0.1:2375');
            $client = new DockerClient();

            $this->assertSame('tcp://127.0.0.1:2375', $client->getHost());
        } finally {
            putenv('DOCKER_HOST='.$backup);
        }
    }
}

class BaseCommandHarness
{
    use BaseCommand {
        execute as public;
    }
}
