<?php

namespace Tests\Unit\Docker\Types;

use PHPUnit\Framework\TestCase;
use stdClass;
use Testcontainers\Docker\Types\ContainerId;
use Testcontainers\Exceptions\InvalidFormatException;

final class ContainerIdTest extends TestCase
{
    /**
     * @dataProvider providerIsValidReturnsWhetherValueIsValid
     * @param array{}|bool|int|null|stdClass|string $value
     * @param bool                                  $expected
     * @return void
     */
    public function testIsValidReturnsWhetherValueIsValid($value, $expected)
    {
        self::assertSame($expected, ContainerId::isValid($value));
    }

    /**
     * @return array<string, array{array{}|bool|int|null|stdClass|string, bool}>
     */
    public static function providerIsValidReturnsWhetherValueIsValid()
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
     * @dataProvider providerConstructPreservesValidIdentifier
     * @param string $value
     * @return void
     */
    public function testConstructPreservesValidIdentifier($value)
    {
        $identifier = new ContainerId($value);

        self::assertSame($value, $identifier->toString());
    }

    /**
     * @dataProvider providerFromStringPreservesValidIdentifier
     * @param string $value
     * @return void
     */
    public function testFromStringPreservesValidIdentifier($value)
    {
        self::assertSame($value, ContainerId::fromString($value)->toString());
    }

    /**
     * @return array<string, array{string}>
     */
    public static function providerConstructPreservesValidIdentifier()
    {
        return [
            'short ID' => ['012345abcdef'],
            'full ID' => [str_repeat('0123456789abcdef', 4)],
        ];
    }

    /**
     * @return array<string, array{string}>
     */
    public static function providerFromStringPreservesValidIdentifier()
    {
        return [
            'short ID' => ['012345abcdef'],
            'full ID' => [str_repeat('0123456789abcdef', 4)],
        ];
    }

    /**
     * @dataProvider providerConstructRejectsInvalidIdentifier
     * @param string $value
     * @return void
     */
    public function testConstructRejectsInvalidIdentifier($value)
    {
        $this->expectException(InvalidFormatException::class);

        new ContainerId($value);
    }

    /**
     * @dataProvider providerFromStringRejectsInvalidIdentifier
     * @param string $value
     * @return void
     */
    public function testFromStringRejectsInvalidIdentifier($value)
    {
        $this->expectException(InvalidFormatException::class);

        ContainerId::fromString($value);
    }

    /**
     * @return array<string, array{string}>
     */
    public static function providerConstructRejectsInvalidIdentifier()
    {
        return [
            'empty string' => [''],
            'invalid length' => ['012345abcde'],
            'invalid character' => ['012345abcdeg'],
            'uppercase characters' => ['012345ABCDEF'],
        ];
    }

    /**
     * @return array<string, array{string}>
     */
    public static function providerFromStringRejectsInvalidIdentifier()
    {
        return [
            'empty string' => [''],
            'invalid length' => ['012345abcde'],
            'invalid character' => ['012345abcdeg'],
            'uppercase characters' => ['012345ABCDEF'],
        ];
    }
}
