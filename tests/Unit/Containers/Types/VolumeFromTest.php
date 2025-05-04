<?php

/** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Unit\Containers\Types;

use PHPUnit\Framework\TestCase;
use Testcontainers\Containers\Types\BindMode;
use Testcontainers\Containers\Types\VolumeFrom;
use Testcontainers\Exceptions\InvalidFormatException;

class VolumeFromTest extends TestCase
{
    public function testVolumeFrom()
    {
        $volumeFrom = new VolumeFrom('container', BindMode::READ_ONLY());

        $this->assertSame('container', $volumeFrom->name);
        $this->assertSame(BindMode::$READ_ONLY, (string) $volumeFrom->mode);
    }

    public function testFromString()
    {
        $volumeFrom = VolumeFrom::fromString('name:ro');

        $this->assertSame('name', $volumeFrom->name);
        $this->assertSame(BindMode::$READ_ONLY, (string) $volumeFrom->mode);
    }

    public function testFromStringOnlyName()
    {
        $volumeFrom = VolumeFrom::fromString('name');

        $this->assertSame('name', $volumeFrom->name);
        $this->assertSame(BindMode::$READ_WRITE, (string) $volumeFrom->mode);
    }

    public function testFromStringFailedParsing()
    {
        $this->expectException(InvalidFormatException::class);

        VolumeFrom::fromString('name:foo');
    }


    public function testFromArray()
    {
        $volumeFrom = VolumeFrom::fromArray([
            'name' => 'name',
            'mode' => BindMode::READ_WRITE(),
        ]);

        $this->assertSame('name', $volumeFrom->name);
        $this->assertSame(BindMode::$READ_WRITE, (string) $volumeFrom->mode);
    }

    public function testToString()
    {
        $volumeFrom = new VolumeFrom('name', BindMode::READ_ONLY());

        $this->assertSame('name:ro', (string) $volumeFrom);
    }
}
