<?php

namespace Tests\Unit\Docker;

use PHPUnit\Framework\TestCase;
use Testcontainers\Docker\DockerClient;

/**
 * @internal
 *
 * @coversNothing
 */
class DockerClientTest extends TestCase
{
    public function testClone()
    {
        $client = new DockerClient();
        $cloned = clone $client;

        $this->assertNotSame($client, $cloned);
        $this->assertEquals($client, $cloned);
    }
}
