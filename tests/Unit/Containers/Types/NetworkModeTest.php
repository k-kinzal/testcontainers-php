<?php

namespace Tests\Unit\Containers\Types;

use PHPUnit\Framework\TestCase;
use Testcontainers\Containers\Types\NetworkMode;

/**
 * @internal
 *
 * @coversNothing
 */
class NetworkModeTest extends TestCase
{
    public function testNetworkModeHost()
    {
        $mode = new NetworkMode(NetworkMode::$HOST);

        $this->assertSame(NetworkMode::$HOST, $mode->toString());
    }

    public function testNetworkModeBridge()
    {
        $mode = new NetworkMode(NetworkMode::$BRIDGE);

        $this->assertSame(NetworkMode::$BRIDGE, $mode->toString());
    }

    public function testNetworkModeNone()
    {
        $mode = new NetworkMode(NetworkMode::$NONE);

        $this->assertSame(NetworkMode::$NONE, $mode->toString());
    }

    public function testNetworkModeCustom()
    {
        $mode = new NetworkMode('custom');

        $this->assertSame('custom', $mode->toString());
    }

    public function testHost()
    {
        $mode = NetworkMode::HOST();

        $this->assertSame(NetworkMode::$HOST, $mode->toString());
    }

    public function testBridge()
    {
        $mode = NetworkMode::BRIDGE();

        $this->assertSame(NetworkMode::$BRIDGE, $mode->toString());
    }

    public function testNone()
    {
        $mode = NetworkMode::NONE();

        $this->assertSame(NetworkMode::$NONE, $mode->toString());
    }

    public function testFromStringWithHost()
    {
        $mode = NetworkMode::fromString(NetworkMode::$HOST);

        $this->assertSame(NetworkMode::$HOST, $mode->toString());
    }

    public function testFromStringWithBridge()
    {
        $mode = NetworkMode::fromString(NetworkMode::$BRIDGE);

        $this->assertSame(NetworkMode::$BRIDGE, $mode->toString());
    }

    public function testFromStringWithNone()
    {
        $mode = NetworkMode::fromString(NetworkMode::$NONE);

        $this->assertSame(NetworkMode::$NONE, $mode->toString());
    }

    public function testFromStringWithCustom()
    {
        $mode = NetworkMode::fromString('custom');

        $this->assertSame('custom', $mode->toString());
    }
}
