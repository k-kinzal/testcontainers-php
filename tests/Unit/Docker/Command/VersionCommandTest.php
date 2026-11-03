<?php

namespace Tests\Unit\Docker\Command;

use PHPUnit\Framework\TestCase;
use Testcontainers\Docker\Command\VersionCommand;
use Testcontainers\Docker\DockerClient;
use Testcontainers\Docker\Output\DockerVersionOutput;

final class VersionCommandTest extends TestCase
{
    /**
     * @return void
     */
    public function testHasVersionCommandTrait()
    {
        $uses = class_uses(DockerClient::class);
        self::assertNotFalse($uses);

        $this->assertContains(VersionCommand::class, $uses);
    }

    /**
     * @return void
     */
    public function testVersion()
    {
        $client = new DockerClient();
        $output = $client->version();

        $this->assertInstanceOf(DockerVersionOutput::class, $output);
        $this->assertSame(0, $output->getExitCode());
        $this->assertNotNull($output->getClientVersion());
        $version = $output->getClientVersion();
        $this->assertNotNull($version);
        $this->assertTrue(preg_match('/^\d+\.\d+/', $version) === 1);
    }
}
