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

        self::assertNotSame($client, $cloned);
        self::assertEquals($client, $cloned);
        self::assertSame(42, $cloned->getTimeout());
    }
}
