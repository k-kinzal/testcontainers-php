<?php

namespace Tests\Unit\Containers\Types;

use PHPUnit\Framework\TestCase;
use Testcontainers\Containers\Types\Mount;

class MountTest extends TestCase
{
    public function testMount()
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

        $this->assertSame('bind', $mount->type);
        $this->assertSame('/host/path', $mount->source);
        $this->assertSame('/container/path', $mount->destination);
        $this->assertSame('/sub/path', $mount->subpath);
        $this->assertTrue($mount->readonly);
        $this->assertTrue($mount->nocopy);
        $this->assertSame([], $mount->opt);
    }

    public function testFromStringParsesTargetOnlyBindMountString()
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $mount = Mount::fromString('/container/path');

        $this->assertSame('bind', $mount->type);
        $this->assertNull($mount->source);
        $this->assertSame('/container/path', $mount->destination);
        $this->assertNull($mount->subpath);
        $this->assertFalse($mount->readonly);
        $this->assertFalse($mount->nocopy);
        $this->assertSame([], $mount->opt);
    }

    public function testFromStringParsesCompleteBindMountString()
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $mount = Mount::fromString('/host/path:/container/path:ro');

        $this->assertSame('bind', $mount->type);
        $this->assertSame('/host/path', $mount->source);
        $this->assertSame('/container/path', $mount->destination);
        $this->assertNull($mount->subpath);
        $this->assertTrue($mount->readonly);
        $this->assertFalse($mount->nocopy);
        $this->assertSame([], $mount->opt);
    }

    public function testFromStringParsesTargetOnlyMountString()
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $mount = Mount::fromString('target=/container/path');

        $this->assertSame('bind', $mount->type);
        $this->assertNull($mount->source);
        $this->assertSame('/container/path', $mount->destination);
        $this->assertNull($mount->subpath);
        $this->assertFalse($mount->readonly);
        $this->assertFalse($mount->nocopy);
        $this->assertSame([], $mount->opt);
    }

    public function testFromStringParsesCompleteMountString()
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $mount = Mount::fromString('type=volume,source=/host/path,destination=/container/path,volume-subpath=/sub/path,readonly,volume-nocopy');

        $this->assertSame('volume', $mount->type);
        $this->assertSame('/host/path', $mount->source);
        $this->assertSame('/container/path', $mount->destination);
        $this->assertSame('/sub/path', $mount->subpath);
        $this->assertTrue($mount->readonly);
        $this->assertTrue($mount->nocopy);
        $this->assertSame([], $mount->opt);
    }

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

        $this->assertSame('bind', $mount->type);
        $this->assertSame('/host/path', $mount->source);
        $this->assertSame('/container/path', $mount->destination);
        $this->assertSame('/sub/path', $mount->subpath);
        $this->assertTrue($mount->readonly);
        $this->assertTrue($mount->nocopy);
        $this->assertSame([], $mount->opt);
    }

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

        $this->assertSame('type=bind,source=/host/path,destination=/container/path,volume-subpath=/sub/path,readonly,volume-nocopy', (string) $mount);
    }
}
