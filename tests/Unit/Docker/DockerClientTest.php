<?php

namespace Tests\Unit\Docker;

use PHPUnit\Framework\TestCase;
use Testcontainers\Docker\DockerClient;
use Testcontainers\Docker\Output\DockerNetworkCreateOutput;
use Testcontainers\Testcontainers;
use Tests\Images\DinD;

class DockerClientTest extends TestCase
{
    public function testNetworkCreate()
    {
        $instance = Testcontainers::run(DinD::class);

        $client = new DockerClient();
        $client->withGlobalOptions([
            'host' => 'tcp://' . $instance->getHost() . ':' . $instance->getMappedPort(2375),
        ]);
        $output = $client->networkCreate('test-network');

        $this->assertInstanceOf(DockerNetworkCreateOutput::class, $output);
        $this->assertSame(0, $output->getExitCode());
        $this->assertTrue(preg_match('/^[0-9a-f]{64}/', $output->getOutput()) === 1);
    }
}
