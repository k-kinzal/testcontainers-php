<?php

namespace Tests\Unit\Docker\Command;

use PHPUnit\Framework\TestCase;
use Testcontainers\Docker\DockerClient;

class BaseCommandTest extends TestCase
{
    public function testGetHostFromDefault()
    {
        $client = new DockerClient();

        $this->assertSame('unix:///var/run/docker.sock', $client->getHost());
    }

    public function testGetHostFromOptions()
    {
        $client = (new DockerClient())
            ->withGlobalOptions([
                'host' => 'tcp://127.0.0.1:2375',
            ]);

        $this->assertSame('tcp://127.0.0.1:2375', $client->getHost());
    }

    public function testGetHostFromEnv()
    {
        $client = (new DockerClient())
            ->withEnv([
                'DOCKER_HOST' => 'tcp://127.0.0.1:2375',
            ]);

        $this->assertSame('tcp://127.0.0.1:2375', $client->getHost());
    }

    /**
     * @runInSeparateProcess
     */
    public function testGetHostFromGlobalEnv()
    {
        putenv('DOCKER_HOST=tcp://127.0.0.1:2375');
        $client = new DockerClient();

        $this->assertSame('tcp://127.0.0.1:2375', $client->getHost());
    }
}
