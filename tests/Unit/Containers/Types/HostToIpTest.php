<?php

/** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Unit\Containers\Types;

use PHPUnit\Framework\TestCase;
use Testcontainers\Containers\Types\HostToIp;
use Testcontainers\Exceptions\InvalidFormatException;
use UnexpectedValueException;

final class HostToIpTest extends TestCase
{
    /**
     * @return void
     */
    public function testConstruct()
    {
        $value = new HostToIp('docker.internal', '127.0.0.1');

        $this->assertSame('docker.internal', $value->host);
        $this->assertSame('127.0.0.1', $value->ip);
    }

    /**
     * @return void
     */
    public function testFromString()
    {
        $value = HostToIp::fromString('docker.internal:127.0.0.1');

        $this->assertSame('docker.internal', $value->host);
        $this->assertSame('127.0.0.1', $value->ip);
    }

    /**
     * @return void
     */
    public function testFromStringWithInvalidFormat()
    {
        $this->expectExceptionMessage('Invalid format: `"docker.internal"`, expects: `host:ip`');
        $this->expectException(InvalidFormatException::class);

        HostToIp::fromString('docker.internal');
    }

    /**
     * @return void
     */
    public function testFromArray()
    {
        $value = HostToIp::fromArray(['hostname' => 'docker.internal', 'ipAddress' => '127.0.0.1']);

        $this->assertSame('docker.internal', $value->host);
        $this->assertSame('127.0.0.1', $value->ip);
    }

    /**
     * @return void
     */
    public function testToString()
    {
        $value = new HostToIp('docker.internal', '127.0.0.1');

        $this->assertSame('docker.internal:127.0.0.1', (string) $value);
    }

    /**
     * @return void
     */
    public function testFromArrayRejectsMissingIpAddress()
    {
        $this->expectException(UnexpectedValueException::class);

        HostToIp::fromArray(['hostname' => 'host']);
    }
}
