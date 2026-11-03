<?php

/** @noinspection PhpUnhandledExceptionInspection */

namespace Tests\Unit\Containers\Types;

use PHPUnit\Framework\TestCase;
use Testcontainers\Containers\Types\BindMode;
use Testcontainers\Containers\Types\VolumeFrom;
use Testcontainers\Exceptions\InvalidFormatException;
use UnexpectedValueException;

final class VolumeFromTest extends TestCase
{
    /**
     * @return void
     */
    public function testConstruct()
    {
        $volumeFrom = new VolumeFrom('container', BindMode::READ_ONLY());

        self::assertSame('container', $volumeFrom->name);
        self::assertSame(BindMode::$READ_ONLY, (string) $volumeFrom->mode);
    }

    /**
     * @return void
     */
    public function testFromString()
    {
        $volumeFrom = VolumeFrom::fromString('name:ro');

        self::assertSame('name', $volumeFrom->name);
        self::assertSame(BindMode::$READ_ONLY, (string) $volumeFrom->mode);
    }

    /**
     * @return void
     */
    public function testFromStringOnlyName()
    {
        $volumeFrom = VolumeFrom::fromString('name');

        self::assertSame('name', $volumeFrom->name);
        self::assertSame(BindMode::$READ_WRITE, (string) $volumeFrom->mode);
    }

    /**
     * @return void
     */
    public function testFromStringFailedParsing()
    {
        $this->expectException(InvalidFormatException::class);

        VolumeFrom::fromString('name:foo');
    }

    /**
     * @return void
     */
    public function testFromArray()
    {
        $volumeFrom = VolumeFrom::fromArray([
            'name' => 'name',
            'mode' => BindMode::READ_WRITE(),
        ]);

        self::assertSame('name', $volumeFrom->name);
        self::assertSame(BindMode::$READ_WRITE, (string) $volumeFrom->mode);
    }

    /**
     * @return void
     */
    public function testToString()
    {
        $volumeFrom = new VolumeFrom('name', BindMode::READ_ONLY());

        self::assertSame('name:ro', (string) $volumeFrom);
    }

    /**
     * @return void
     */
    public function testFromArrayRejectsBooleanMode()
    {
        $this->expectException(UnexpectedValueException::class);

        VolumeFrom::fromArray(['name' => 'volume', 'mode' => false]);
    }
}
