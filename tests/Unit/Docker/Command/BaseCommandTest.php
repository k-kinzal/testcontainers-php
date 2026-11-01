<?php

namespace Tests\Unit\Docker\Command;

use PHPUnit\Framework\TestCase;
use Testcontainers\Docker\DockerClient;

/**
 * @internal
 *
 * @coversNothing
 */
class BaseCommandTest extends TestCase
{
    public function testWithProcOptionsExecutesCommand()
    {
        $client = (new DockerClient())
            ->withCommand(PHP_BINARY)
            ->withGlobalOptions(['version' => true])
            ->withProcOptions(['blocking_pipes' => false])
        ;
        $output = $client->version();

        $this->assertSame(0, $output->getExitCode());
        $this->assertSame(0, strpos($output->getOutput(), 'PHP '));
    }

    public function testGetHostFromDefault()
    {
        $backup = getenv('DOCKER_HOST');

        try {
            putenv('DOCKER_HOST');
            $client = new DockerClient();

            $this->assertSame('unix:///var/run/docker.sock', $client->getHost());
        } finally {
            putenv($backup === false ? 'DOCKER_HOST' : 'DOCKER_HOST='.$backup);
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
            putenv($backup === false ? 'DOCKER_HOST' : 'DOCKER_HOST='.$backup);
        }
    }
}
