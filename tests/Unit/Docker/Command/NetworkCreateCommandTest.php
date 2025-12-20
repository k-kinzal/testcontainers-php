<?php

namespace Tests\Unit\Docker\Command;

use PHPUnit\Framework\TestCase;
use Testcontainers\Docker\Command\NetworkCreateCommand;
use Testcontainers\Docker\DockerClient;
use Testcontainers\Docker\Output\DockerNetworkCreateOutput;
use Testcontainers\Testcontainers;
use Tests\Images\DinD;

/**
 * @internal
 *
 * @coversNothing
 */
class NetworkCreateCommandTest extends TestCase
{
    public function testHasNetworkCreateCommandTrait()
    {
        $uses = class_uses(DockerClient::class);

        $this->assertContains(NetworkCreateCommand::class, $uses);
    }

    public function testNetworkCreate()
    {
        $instance = Testcontainers::run(DinD::class);

        $client = new DockerClient();
        $client->withGlobalOptions([
            'host' => 'tcp://'.$instance->getHost().':'.$instance->getMappedPort(2375),
            'config' => __ROOT__.'/config.json',
        ]);
        $output = $client->networkCreate('test-network');

        $this->assertInstanceOf(DockerNetworkCreateOutput::class, $output);
        $this->assertSame(0, $output->getExitCode());
        $this->assertTrue(preg_match('/^[0-9a-f]{64}/', $output->getNetworkId()) === 1);
    }
}
