<?php

namespace Tests\Unit\Containers\Types;

use PHPUnit\Framework\TestCase;
use Testcontainers\Containers\Types\NetworkMode;

final class NetworkModeTest extends TestCase
{
    /**
     * @return void
     */
    public function testConstructHost()
    {
        $mode = new NetworkMode(NetworkMode::$HOST);

        $this->assertSame(NetworkMode::$HOST, $mode->toString());
    }

    /**
     * @return void
     */
    public function testConstructBridge()
    {
        $mode = new NetworkMode(NetworkMode::$BRIDGE);

        $this->assertSame(NetworkMode::$BRIDGE, $mode->toString());
    }

    /**
     * @return void
     */
    public function testConstructNone()
    {
        $mode = new NetworkMode(NetworkMode::$NONE);

        $this->assertSame(NetworkMode::$NONE, $mode->toString());
    }

    /**
     * @return void
     */
    public function testConstructCustom()
    {
        $mode = new NetworkMode('custom');

        $this->assertSame('custom', $mode->toString());
    }

    /**
     * @return void
     */
    public function testHost()
    {
        $mode = NetworkMode::HOST();

        $this->assertSame(NetworkMode::$HOST, $mode->toString());
    }

    /**
     * @return void
     */
    public function testBridge()
    {
        $mode = NetworkMode::BRIDGE();

        $this->assertSame(NetworkMode::$BRIDGE, $mode->toString());
    }

    /**
     * @return void
     */
    public function testNone()
    {
        $mode = NetworkMode::NONE();

        $this->assertSame(NetworkMode::$NONE, $mode->toString());
    }

    /**
     * @return void
     */
    public function testFromStringWithHost()
    {
        $mode = NetworkMode::fromString(NetworkMode::$HOST);

        $this->assertSame(NetworkMode::$HOST, $mode->toString());
    }

    /**
     * @return void
     */
    public function testFromStringWithBridge()
    {
        $mode = NetworkMode::fromString(NetworkMode::$BRIDGE);

        $this->assertSame(NetworkMode::$BRIDGE, $mode->toString());
    }

    /**
     * @return void
     */
    public function testFromStringWithNone()
    {
        $mode = NetworkMode::fromString(NetworkMode::$NONE);

        $this->assertSame(NetworkMode::$NONE, $mode->toString());
    }

    /**
     * @return void
     */
    public function testFromStringWithCustom()
    {
        $mode = NetworkMode::fromString('custom');

        $this->assertSame('custom', $mode->toString());
    }
}
