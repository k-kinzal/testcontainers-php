<?php

namespace Tests\Unit\Docker;

use PHPUnit\Framework\TestCase;
use Testcontainers\Docker\DockerClient;

final class DockerClientTest extends TestCase
{
    /**
     * @return void
     */
    public function testWithTimeoutConfigurationSurvivesCloning()
    {
        $client = (new DockerClient())->withTimeout(42);
        $cloned = clone $client;

        $this->assertNotSame($client, $cloned);
        $this->assertEquals($client, $cloned);
        $this->assertSame(42, $cloned->getTimeout());
    }
}
