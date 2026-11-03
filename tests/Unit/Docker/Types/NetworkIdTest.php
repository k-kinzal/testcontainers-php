<?php

namespace Tests\Unit\Docker\Types;

use PHPUnit\Framework\TestCase;
use stdClass;
use Testcontainers\Docker\Types\NetworkId;
use Testcontainers\Exceptions\InvalidFormatException;

/**
 * @internal
 *
 * @coversNothing
 */
final class NetworkIdTest extends TestCase
{
    /**
     * @dataProvider providerValues
     * @param array{}|bool|int|null|stdClass|string $value
     * @param bool                                  $expected
     * @return void
     */
    public function testIsValidReturnsWhetherValueIsValid($value, $expected)
    {
        $this->assertSame($expected, NetworkId::isValid($value));
    }

    /**
     * @return array<string, array{array{}|bool|int|null|stdClass|string, bool}>
     */
    public static function providerValues()
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
     * @dataProvider providerValidIdentifiers
     * @param string $value
     * @return void
     */
    public function testConstructPreservesValidIdentifier($value)
    {
        $identifier = new NetworkId($value);

        $this->assertSame($value, $identifier->toString());
    }

    /**
     * @dataProvider providerValidIdentifiers
     * @param string $value
     * @return void
     */
    public function testFromStringPreservesValidIdentifier($value)
    {
        $this->assertSame($value, NetworkId::fromString($value)->toString());
    }

    /**
     * @return array<string, array{string}>
     */
    public static function providerValidIdentifiers()
    {
        return [
            'short ID' => ['012345abcdef'],
            'full ID' => [str_repeat('0123456789abcdef', 4)],
        ];
    }

    /**
     * @dataProvider providerInvalidIdentifiers
     * @param string $value
     * @return void
     */
    public function testConstructRejectsInvalidIdentifier($value)
    {
        $this->expectException(InvalidFormatException::class);

        new NetworkId($value);
    }

    /**
     * @dataProvider providerInvalidIdentifiers
     * @param string $value
     * @return void
     */
    public function testFromStringRejectsInvalidIdentifier($value)
    {
        $this->expectException(InvalidFormatException::class);

        NetworkId::fromString($value);
    }

    /**
     * @return array<string, array{string}>
     */
    public static function providerInvalidIdentifiers()
    {
        return [
            'empty string' => [''],
            'invalid length' => ['012345abcde'],
            'invalid character' => ['012345abcdeg'],
            'uppercase characters' => ['012345ABCDEF'],
        ];
    }
}
