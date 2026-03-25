<?php

namespace Tests\Unit\Docker\Command;

use PHPUnit\Framework\TestCase;
use Testcontainers\Docker\Command\VersionCommand;
use Testcontainers\Docker\DockerClient;
use Testcontainers\Docker\Output\DockerVersionOutput;

/**
 * @internal
 *
 * @coversNothing
 */
class VersionCommandTest extends TestCase
{
    public function testHasVersionCommandTrait()
    {
        $uses = class_uses(DockerClient::class);

        $this->assertContains(VersionCommand::class, $uses);
    }

    public function testVersion()
    {
        $client = new DockerClient();
        $output = $client->version();

        $this->assertInstanceOf(DockerVersionOutput::class, $output);
        $this->assertSame(0, $output->getExitCode());
        $this->assertNotNull($output->getClientVersion());
        $this->assertMatchesRegularExpression('/^\d+\.\d+/', $output->getClientVersion());
    }
}
