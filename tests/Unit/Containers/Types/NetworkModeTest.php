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

        self::assertSame(NetworkMode::$HOST, $mode->toString());
    }

    /**
     * @return void
     */
    public function testConstructBridge()
    {
        $mode = new NetworkMode(NetworkMode::$BRIDGE);

        self::assertSame(NetworkMode::$BRIDGE, $mode->toString());
    }

    /**
     * @return void
     */
    public function testConstructNone()
    {
        $mode = new NetworkMode(NetworkMode::$NONE);

        self::assertSame(NetworkMode::$NONE, $mode->toString());
    }

    /**
     * @return void
     */
    public function testConstructCustom()
    {
        $mode = new NetworkMode('custom');

        self::assertSame('custom', $mode->toString());
    }

    /**
     * @return void
     */
    public function testHost()
    {
        $mode = NetworkMode::HOST();

        self::assertSame(NetworkMode::$HOST, $mode->toString());
    }

    /**
     * @return void
     */
    public function testBridge()
    {
        $mode = NetworkMode::BRIDGE();

        self::assertSame(NetworkMode::$BRIDGE, $mode->toString());
    }

    /**
     * @return void
     */
    public function testNone()
    {
        $mode = NetworkMode::NONE();

        self::assertSame(NetworkMode::$NONE, $mode->toString());
    }

    /**
     * @return void
     */
    public function testFromStringWithHost()
    {
        $mode = NetworkMode::fromString(NetworkMode::$HOST);

        self::assertSame(NetworkMode::$HOST, $mode->toString());
    }

    /**
     * @return void
     */
    public function testFromStringWithBridge()
    {
        $mode = NetworkMode::fromString(NetworkMode::$BRIDGE);

        self::assertSame(NetworkMode::$BRIDGE, $mode->toString());
    }

    /**
     * @return void
     */
    public function testFromStringWithNone()
    {
        $mode = NetworkMode::fromString(NetworkMode::$NONE);

        self::assertSame(NetworkMode::$NONE, $mode->toString());
    }

    /**
     * @return void
     */
    public function testFromStringWithCustom()
    {
        $mode = NetworkMode::fromString('custom');

        self::assertSame('custom', $mode->toString());
    }
}
