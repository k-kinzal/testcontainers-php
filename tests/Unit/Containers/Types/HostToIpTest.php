<?php

namespace Tests\Unit\Containers\Types;

use PHPUnit\Framework\TestCase;
use Testcontainers\Containers\Types\HostToIp;
use Testcontainers\Exceptions\InvalidFormatException;

class HostToIpTest extends TestCase
{
    public function testHostToIp()
    {
        $value = new HostToIp('docker.internal', '127.0.0.1');

        $this->assertSame('docker.internal', $value->host);
        $this->assertSame('127.0.0.1', $value->ip);
    }

    public function testFromString()
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $value = HostToIp::fromString('docker.internal:127.0.0.1');

        $this->assertSame('docker.internal', $value->host);
        $this->assertSame('127.0.0.1', $value->ip);
    }

    public function testFromStringWithInvalidFormat()
    {
        $this->expectExceptionMessage('Invalid format: `"docker.internal"`, expects: `host:ip`');
        $this->expectException(InvalidFormatException::class);

        /** @noinspection PhpUnhandledExceptionInspection */
        HostToIp::fromString('docker.internal');
    }

    public function testFromArray()
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $value = HostToIp::fromArray(['hostname' => 'docker.internal', 'ipAddress' => '127.0.0.1']);

        $this->assertSame('docker.internal', $value->host);
        $this->assertSame('127.0.0.1', $value->ip);
    }

    public function testFromArrayWithInvalidFormat()
    {
        $this->expectExceptionMessage('Invalid format: `[]`, expects: `["hostname": string, "ipAddress": string]`');
        $this->expectException(InvalidFormatException::class);

        /** @noinspection PhpUnhandledExceptionInspection */
        HostToIp::fromArray([]);
    }

    public function testToString()
    {
        $value = new HostToIp('docker.internal', '127.0.0.1');

        $this->assertSame('docker.internal:127.0.0.1', (string)$value);
    }
}
