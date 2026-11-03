<?php

/** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Unit\Containers\Types;

use PHPUnit\Framework\TestCase;
use Testcontainers\Containers\Types\Mount;
use UnexpectedValueException;

final class MountTest extends TestCase
{
    /**
     * @return void
     */
    public function testConstruct()
    {
        $mount = new Mount(
            'bind',
            '/host/path',
            '/container/path',
            '/sub/path',
            true,
            true,
            []
        );

        self::assertSame('bind', $mount->type);
        self::assertSame('/host/path', $mount->source);
        self::assertSame('/container/path', $mount->destination);
        self::assertSame('/sub/path', $mount->subpath);
        self::assertTrue($mount->readonly);
        self::assertTrue($mount->nocopy);
        self::assertSame([], $mount->opt);
    }

    /**
     * @return void
     */
    public function testFromStringParsesTargetOnlyBindMountString()
    {
        $mount = Mount::fromString('/container/path');

        self::assertSame('bind', $mount->type);
        self::assertNull($mount->source);
        self::assertSame('/container/path', $mount->destination);
        self::assertNull($mount->subpath);
        self::assertFalse($mount->readonly);
        self::assertFalse($mount->nocopy);
        self::assertSame([], $mount->opt);
    }

    /**
     * @return void
     */
    public function testFromStringParsesCompleteBindMountString()
    {
        $mount = Mount::fromString('/host/path:/container/path:ro');

        self::assertSame('bind', $mount->type);
        self::assertSame('/host/path', $mount->source);
        self::assertSame('/container/path', $mount->destination);
        self::assertNull($mount->subpath);
        self::assertTrue($mount->readonly);
        self::assertFalse($mount->nocopy);
        self::assertSame([], $mount->opt);
    }

    /**
     * @return void
     */
    public function testFromStringParsesTargetOnlyMountString()
    {
        $mount = Mount::fromString('target=/container/path');

        self::assertSame('bind', $mount->type);
        self::assertNull($mount->source);
        self::assertSame('/container/path', $mount->destination);
        self::assertNull($mount->subpath);
        self::assertFalse($mount->readonly);
        self::assertFalse($mount->nocopy);
        self::assertSame([], $mount->opt);
    }

    /**
     * @return void
     */
    public function testFromStringParsesCompleteMountString()
    {
        $mount = Mount::fromString('type=volume,source=/host/path,destination=/container/path,volume-subpath=/sub/path,readonly,volume-nocopy');

        self::assertSame('volume', $mount->type);
        self::assertSame('/host/path', $mount->source);
        self::assertSame('/container/path', $mount->destination);
        self::assertSame('/sub/path', $mount->subpath);
        self::assertTrue($mount->readonly);
        self::assertTrue($mount->nocopy);
        self::assertSame([], $mount->opt);
    }

    /**
     * @return void
     */
    public function testFromArray()
    {
        $mount = Mount::fromArray([
            'type' => 'bind',
            'source' => '/host/path',
            'destination' => '/container/path',
            'subpath' => '/sub/path',
            'readonly' => true,
            'nocopy' => true,
            'opt' => [],
        ]);

        self::assertSame('bind', $mount->type);
        self::assertSame('/host/path', $mount->source);
        self::assertSame('/container/path', $mount->destination);
        self::assertSame('/sub/path', $mount->subpath);
        self::assertTrue($mount->readonly);
        self::assertTrue($mount->nocopy);
        self::assertSame([], $mount->opt);
    }

    /**
     * @return void
     */
    public function testToString()
    {
        $mount = new Mount(
            'bind',
            '/host/path',
            '/container/path',
            '/sub/path',
            true,
            true,
            []
        );

        self::assertSame('type=bind,source=/host/path,destination=/container/path,volume-subpath=/sub/path,readonly,volume-nocopy', (string) $mount);
    }

    /**
     * @return void
     */
    public function testFromArrayRejectsStringReadonlyFlag()
    {
        $this->expectException(UnexpectedValueException::class);

        Mount::fromArray(['target' => '/data', 'readonly' => 'false']);
    }
}
