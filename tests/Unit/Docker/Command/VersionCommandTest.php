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
    public function testVersionIsProvidedByVersionCommandTrait()
    {
        $uses = class_uses(DockerClient::class);
        self::assertNotFalse($uses);
        self::assertContains(VersionCommand::class, $uses);
    }

    /**
     * @return void
     */
    public function testVersion()
    {
        $client = new DockerClient();
        $output = $client->version();

        self::assertInstanceOf(DockerVersionOutput::class, $output);
        self::assertSame(0, $output->getExitCode());
        self::assertNotNull($output->getClientVersion());
        $version = $output->getClientVersion();
        self::assertNotNull($version);
        self::assertTrue(preg_match('/^\d+\.\d+/', $version) === 1);
    }
}
