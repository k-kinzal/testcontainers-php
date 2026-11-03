<?php

namespace Tests\Unit\Docker\Command;

use PHPUnit\Framework\TestCase;
use Testcontainers\Docker\Command\NetworkCreateCommand;
use Testcontainers\Docker\DockerClient;
use Testcontainers\Docker\Output\DockerNetworkCreateOutput;
use Testcontainers\Testcontainers;
use Tests\Images\DinD;

final class NetworkCreateCommandTest extends TestCase
{
    /**
     * @return void
     */
    public function testNetworkCreateIsProvidedByNetworkCreateCommandTrait()
    {
        $uses = class_uses(DockerClient::class);
        self::assertNotFalse($uses);

        $this->assertContains(NetworkCreateCommand::class, $uses);
    }

    /**
     * @return void
     */
    public function testNetworkCreate()
    {
        $instance = Testcontainers::run(DinD::class);
        $mappedPort2375 = $instance->getMappedPort(2375);
        $this->assertNotNull($mappedPort2375);

        $client = new DockerClient();
        $client->withGlobalOptions([
            'host' => 'tcp://'.$instance->getHost().':'.(string) $mappedPort2375,
            'config' => __ROOT__.'/config.json',
        ]);
        $output = $client->networkCreate('test-network');

        $this->assertInstanceOf(DockerNetworkCreateOutput::class, $output);
        $this->assertSame(0, $output->getExitCode());
        $this->assertTrue(preg_match('/^[0-9a-f]{64}/', $output->getNetworkId()->toString()) === 1);
    }
}
