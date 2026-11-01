<?php

namespace Tests\Unit\Docker\Types;

use PHPUnit\Framework\TestCase;
use stdClass;
use Testcontainers\Docker\Types\ContainerId;
use Testcontainers\Exceptions\InvalidFormatException;

/**
 * @internal
 *
 * @coversNothing
 */
class ContainerIdTest extends TestCase
{
    /**
     * @dataProvider provideValues
     * @param mixed $value
     * @param bool  $expected
     */
    public function testIsValidReturnsWhetherValueIsValid($value, $expected)
    {
        $this->assertSame($expected, ContainerId::isValid($value));
    }

    /**
     * @return array
     */
    public static function provideValues()
    {
        return [
            'short ID' => ['012345abcdef', true],
            'full ID' => [str_repeat('0123456789abcdef', 4), true],
            'empty string' => ['', false],
            'invalid length' => ['012345abcde', false],
            'invalid character' => ['012345abcdeg', false],
            'uppercase characters' => ['012345ABCDEF', false],
            'null' => [null, false],
            'boolean' => [false, false],
            'integer' => [123456789012, false],
            'array' => [[], false],
            'object' => [new stdClass(), false],
        ];
    }

    /**
     * @dataProvider provideValidIdentifiers
     * @param string $value
     */
    public function testConstructPreservesValidIdentifier($value)
    {
        $identifier = new ContainerId($value);

        $this->assertSame($value, $identifier->toString());
    }

    /**
     * @dataProvider provideValidIdentifiers
     * @param string $value
     */
    public function testFromStringPreservesValidIdentifier($value)
    {
        $this->assertSame($value, ContainerId::fromString($value)->toString());
    }

    /**
     * @return array
     */
    public static function provideValidIdentifiers()
    {
        return [
            'short ID' => ['012345abcdef'],
            'full ID' => [str_repeat('0123456789abcdef', 4)],
        ];
    }

    /**
     * @dataProvider provideInvalidIdentifiers
     * @param string $value
     */
    public function testConstructRejectsInvalidIdentifier($value)
    {
        $this->expectException(InvalidFormatException::class);

        new ContainerId($value);
    }

    /**
     * @dataProvider provideInvalidIdentifiers
     * @param string $value
     */
    public function testFromStringRejectsInvalidIdentifier($value)
    {
        $this->expectException(InvalidFormatException::class);

        ContainerId::fromString($value);
    }

    /**
     * @return array
     */
    public static function provideInvalidIdentifiers()
    {
        return [
            'empty string' => [''],
            'invalid length' => ['012345abcde'],
            'invalid character' => ['012345abcdeg'],
            'uppercase characters' => ['012345ABCDEF'],
        ];
    }
}
