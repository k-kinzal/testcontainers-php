<?php

namespace Tests\Unit\Utility;

use ArrayIterator;
use PHPUnit\Framework\TestCase;
use SimpleXMLElement;
use stdClass;
use Testcontainers\Exceptions\InvalidFormatException;
use Testcontainers\Utility\Ensure;
use Traversable;
use UnexpectedValueException;

/**
 * @internal
 * @coversNothing
 */
class EnsureTest extends TestCase
{
    use Ensure;

    /**
     * @dataProvider provideStringValidValues
     * @param mixed $value
     * @param mixed $expected
     */
    public function testEnsureStringAcceptsValidValues($value, $expected)
    {
        self::ensureString($value);

        $this->assertSame($expected, $value);
    }

    /**
     * @return array
     */
    public static function provideStringValidValues()
    {
        return [
            'case 1' => ['', ''],
            'case 2' => ['text', 'text'],
            'case 3' => ['0', '0'],
        ];
    }

    /**
     * @dataProvider provideStringInvalidValues
     * @param mixed $value
     */
    public function testEnsureStringRejectsInvalidValues($value)
    {
        $this->expectException(UnexpectedValueException::class);

        self::ensureString($value);
    }

    /**
     * @return array
     */
    public static function provideStringInvalidValues()
    {
        return [
            'case 1' => [null],
            'case 2' => [false],
            'case 3' => [1],
            'case 4' => [[]],
            'case 5' => [new stdClass()],
        ];
    }

    /**
     * @dataProvider provideNonEmptyStringValidValues
     * @param mixed $value
     * @param mixed $expected
     */
    public function testEnsureNonEmptyStringAcceptsValidValues($value, $expected)
    {
        self::ensureNonEmptyString($value);

        $this->assertSame($expected, $value);
    }

    /**
     * @return array
     */
    public static function provideNonEmptyStringValidValues()
    {
        return [
            'case 1' => ['text', 'text'],
            'case 2' => ['0', '0'],
        ];
    }

    /**
     * @dataProvider provideNonEmptyStringInvalidValues
     * @param mixed $value
     */
    public function testEnsureNonEmptyStringRejectsInvalidValues($value)
    {
        $this->expectException(UnexpectedValueException::class);

        self::ensureNonEmptyString($value);
    }

    /**
     * @return array
     */
    public static function provideNonEmptyStringInvalidValues()
    {
        return [
            'case 1' => [''],
            'case 2' => [null],
            'case 3' => [false],
            'case 4' => [1],
        ];
    }

    /**
     * @dataProvider provideNullableStringValidValues
     * @param mixed $value
     * @param mixed $expected
     */
    public function testEnsureNullableStringAcceptsValidValues($value, $expected)
    {
        self::ensureNullableString($value);

        $this->assertSame($expected, $value);
    }

    /**
     * @return array
     */
    public static function provideNullableStringValidValues()
    {
        return [
            'case 1' => [null, null],
            'case 2' => ['', ''],
            'case 3' => ['text', 'text'],
        ];
    }

    /**
     * @dataProvider provideNullableStringInvalidValues
     * @param mixed $value
     */
    public function testEnsureNullableStringRejectsInvalidValues($value)
    {
        $this->expectException(UnexpectedValueException::class);

        self::ensureNullableString($value);
    }

    /**
     * @return array
     */
    public static function provideNullableStringInvalidValues()
    {
        return [
            'case 1' => [false],
            'case 2' => [1],
            'case 3' => [[]],
        ];
    }

    /**
     * @dataProvider provideNullableNonEmptyStringValidValues
     * @param mixed $value
     * @param mixed $expected
     */
    public function testEnsureNullableNonEmptyStringAcceptsValidValues($value, $expected)
    {
        self::ensureNullableNonEmptyString($value);

        $this->assertSame($expected, $value);
    }

    /**
     * @return array
     */
    public static function provideNullableNonEmptyStringValidValues()
    {
        return [
            'case 1' => [null, null],
            'case 2' => ['text', 'text'],
            'case 3' => ['0', '0'],
        ];
    }

    /**
     * @dataProvider provideNullableNonEmptyStringInvalidValues
     * @param mixed $value
     */
    public function testEnsureNullableNonEmptyStringRejectsInvalidValues($value)
    {
        $this->expectException(UnexpectedValueException::class);

        self::ensureNullableNonEmptyString($value);
    }

    /**
     * @return array
     */
    public static function provideNullableNonEmptyStringInvalidValues()
    {
        return [
            'case 1' => [''],
            'case 2' => [false],
            'case 3' => [1],
        ];
    }

    /**
     * @dataProvider provideBoolValidValues
     * @param mixed $value
     * @param mixed $expected
     */
    public function testEnsureBoolAcceptsValidValues($value, $expected)
    {
        self::ensureBool($value);

        $this->assertSame($expected, $value);
    }

    /**
     * @return array
     */
    public static function provideBoolValidValues()
    {
        return [
            'case 1' => [true, true],
            'case 2' => [false, false],
        ];
    }

    /**
     * @dataProvider provideBoolInvalidValues
     * @param mixed $value
     */
    public function testEnsureBoolRejectsInvalidValues($value)
    {
        $this->expectException(UnexpectedValueException::class);

        self::ensureBool($value);
    }

    /**
     * @return array
     */
    public static function provideBoolInvalidValues()
    {
        return [
            'case 1' => [null],
            'case 2' => [0],
            'case 3' => [1],
            'case 4' => ['false'],
            'case 5' => [[]],
        ];
    }

    /**
     * @dataProvider provideNullableBoolValidValues
     * @param mixed $value
     * @param mixed $expected
     */
    public function testEnsureNullableBoolAcceptsValidValues($value, $expected)
    {
        self::ensureNullableBool($value);

        $this->assertSame($expected, $value);
    }

    /**
     * @return array
     */
    public static function provideNullableBoolValidValues()
    {
        return [
            'case 1' => [null, null],
            'case 2' => [true, true],
            'case 3' => [false, false],
        ];
    }

    /**
     * @dataProvider provideNullableBoolInvalidValues
     * @param mixed $value
     */
    public function testEnsureNullableBoolRejectsInvalidValues($value)
    {
        $this->expectException(UnexpectedValueException::class);

        self::ensureNullableBool($value);
    }

    /**
     * @return array
     */
    public static function provideNullableBoolInvalidValues()
    {
        return [
            'case 1' => [0],
            'case 2' => ['false'],
            'case 3' => [[]],
        ];
    }

    /**
     * @dataProvider provideIntValidValues
     * @param mixed $value
     * @param mixed $expected
     */
    public function testEnsureIntAcceptsValidValues($value, $expected)
    {
        self::ensureInt($value);

        $this->assertSame($expected, $value);
    }

    /**
     * @return array
     */
    public static function provideIntValidValues()
    {
        return [
            'case 1' => [0, 0],
            'case 2' => [1, 1],
            'case 3' => [-1, -1],
        ];
    }

    /**
     * @dataProvider provideIntInvalidValues
     * @param mixed $value
     */
    public function testEnsureIntRejectsInvalidValues($value)
    {
        $this->expectException(UnexpectedValueException::class);

        self::ensureInt($value);
    }

    /**
     * @return array
     */
    public static function provideIntInvalidValues()
    {
        return [
            'case 1' => ['1'],
            'case 2' => [1.5],
            'case 3' => [true],
            'case 4' => [null],
        ];
    }

    /**
     * @dataProvider provideIntRangeValidValues
     * @param mixed $value
     * @param mixed $minimum
     * @param mixed $maximum
     * @param mixed $expected
     */
    public function testEnsureIntRangeAcceptsValidValues($value, $minimum, $maximum, $expected)
    {
        self::ensureIntRange($value, $minimum, $maximum);

        $this->assertSame($expected, $value);
    }

    /**
     * @return array
     */
    public static function provideIntRangeValidValues()
    {
        return [
            'case 1' => [0, 0, 10, 0],
            'case 2' => [10, 0, 10, 10],
            'case 3' => [999, 0, null, 999],
        ];
    }

    /**
     * @dataProvider provideIntRangeInvalidValues
     * @param mixed $value
     * @param mixed $minimum
     * @param mixed $maximum
     */
    public function testEnsureIntRangeRejectsInvalidValues($value, $minimum, $maximum)
    {
        $this->expectException(UnexpectedValueException::class);

        self::ensureIntRange($value, $minimum, $maximum);
    }

    /**
     * @return array
     */
    public static function provideIntRangeInvalidValues()
    {
        return [
            'case 1' => [-1, 0, 10],
            'case 2' => [11, 0, 10],
            'case 3' => ['1', 0, 10],
        ];
    }

    /**
     * @dataProvider provideNullableIntRangeValidValues
     * @param mixed $value
     * @param mixed $minimum
     * @param mixed $maximum
     * @param mixed $expected
     */
    public function testEnsureNullableIntRangeAcceptsValidValues($value, $minimum, $maximum, $expected)
    {
        self::ensureNullableIntRange($value, $minimum, $maximum);

        $this->assertSame($expected, $value);
    }

    /**
     * @return array
     */
    public static function provideNullableIntRangeValidValues()
    {
        return [
            'case 1' => [null, 0, 10, null],
            'case 2' => [0, 0, 10, 0],
            'case 3' => [10, 0, 10, 10],
        ];
    }

    /**
     * @dataProvider provideNullableIntRangeInvalidValues
     * @param mixed $value
     * @param mixed $minimum
     * @param mixed $maximum
     */
    public function testEnsureNullableIntRangeRejectsInvalidValues($value, $minimum, $maximum)
    {
        $this->expectException(UnexpectedValueException::class);

        self::ensureNullableIntRange($value, $minimum, $maximum);
    }

    /**
     * @return array
     */
    public static function provideNullableIntRangeInvalidValues()
    {
        return [
            'case 1' => [-1, 0, 10],
            'case 2' => [11, 0, 10],
            'case 3' => [false, 0, 10],
        ];
    }

    /**
     * @dataProvider provideIntOrDigitStringValidValues
     * @param mixed $value
     * @param mixed $expected
     */
    public function testEnsureIntOrDigitStringAcceptsValidValues($value, $expected)
    {
        self::ensureIntOrDigitString($value);

        $this->assertSame($expected, $value);
    }

    /**
     * @return array
     */
    public static function provideIntOrDigitStringValidValues()
    {
        return [
            'case 1' => [80, 80],
            'case 2' => ['080', '080'],
            'case 3' => ['0', '0'],
        ];
    }

    /**
     * @dataProvider provideIntOrDigitStringInvalidValues
     * @param mixed $value
     */
    public function testEnsureIntOrDigitStringRejectsInvalidValues($value)
    {
        $this->expectException(UnexpectedValueException::class);

        self::ensureIntOrDigitString($value);
    }

    /**
     * @return array
     */
    public static function provideIntOrDigitStringInvalidValues()
    {
        return [
            'case 1' => [true],
            'case 2' => [1.5],
            'case 3' => ['1e3'],
            'case 4' => ['-1'],
            'case 5' => ['80x'],
            'case 6' => [''],
        ];
    }

    /**
     * @dataProvider provideIntOrDigitStringRangeValidValues
     * @param mixed $value
     * @param mixed $minimum
     * @param mixed $maximum
     * @param mixed $expected
     */
    public function testEnsureIntOrDigitStringRangeAcceptsValidValues($value, $minimum, $maximum, $expected)
    {
        self::ensureIntOrDigitStringRange($value, $minimum, $maximum);

        $this->assertSame($expected, $value);
    }

    /**
     * @return array
     */
    public static function provideIntOrDigitStringRangeValidValues()
    {
        return [
            'case 1' => [1, 1, 65535, 1],
            'case 2' => ['080', 1, 65535, '080'],
            'case 3' => ['65535', 1, 65535, '65535'],
        ];
    }

    /**
     * @dataProvider provideIntOrDigitStringRangeInvalidValues
     * @param mixed $value
     * @param mixed $minimum
     * @param mixed $maximum
     */
    public function testEnsureIntOrDigitStringRangeRejectsInvalidValues($value, $minimum, $maximum)
    {
        $this->expectException(UnexpectedValueException::class);

        self::ensureIntOrDigitStringRange($value, $minimum, $maximum);
    }

    /**
     * @return array
     */
    public static function provideIntOrDigitStringRangeInvalidValues()
    {
        return [
            'case 1' => [0, 1, 65535],
            'case 2' => [65536, 1, 65535],
            'case 3' => ['99999999999999999999999999', 1, 65535],
            'case 4' => [false, 1, 65535],
        ];
    }

    /**
     * @dataProvider provideDigitStringValidValues
     * @param mixed $value
     * @param mixed $expected
     */
    public function testEnsureDigitStringAcceptsValidValues($value, $expected)
    {
        self::ensureDigitString($value);

        $this->assertSame($expected, $value);
    }

    /**
     * @return array
     */
    public static function provideDigitStringValidValues()
    {
        return [
            'case 1' => ['0', '0'],
            'case 2' => ['080', '080'],
            'case 3' => ['123', '123'],
        ];
    }

    /**
     * @dataProvider provideDigitStringInvalidValues
     * @param mixed $value
     */
    public function testEnsureDigitStringRejectsInvalidValues($value)
    {
        $this->expectException(UnexpectedValueException::class);

        self::ensureDigitString($value);
    }

    /**
     * @return array
     */
    public static function provideDigitStringInvalidValues()
    {
        return [
            'case 1' => [''],
            'case 2' => ['1e3'],
            'case 3' => [' 80'],
            'case 4' => ['80x'],
            'case 5' => [80],
            'case 6' => [true],
        ];
    }

    /**
     * @dataProvider provideDigitStringRangeValidValues
     * @param mixed $value
     * @param mixed $minimum
     * @param mixed $maximum
     * @param mixed $expected
     */
    public function testEnsureDigitStringRangeAcceptsValidValues($value, $minimum, $maximum, $expected)
    {
        self::ensureDigitStringRange($value, $minimum, $maximum);

        $this->assertSame($expected, $value);
    }

    /**
     * @return array
     */
    public static function provideDigitStringRangeValidValues()
    {
        return [
            'case 1' => ['1', 1, 10, '1'],
            'case 2' => ['10', 1, 10, '10'],
        ];
    }

    /**
     * @dataProvider provideDigitStringRangeInvalidValues
     * @param mixed $value
     * @param mixed $minimum
     * @param mixed $maximum
     */
    public function testEnsureDigitStringRangeRejectsInvalidValues($value, $minimum, $maximum)
    {
        $this->expectException(UnexpectedValueException::class);

        self::ensureDigitStringRange($value, $minimum, $maximum);
    }

    /**
     * @return array
     */
    public static function provideDigitStringRangeInvalidValues()
    {
        return [
            'case 1' => ['0', 1, 10],
            'case 2' => ['11', 1, 10],
            'case 3' => ['1e1', 1, 10],
        ];
    }

    /**
     * @dataProvider provideNonNegativeNumberValidValues
     * @param mixed $value
     * @param mixed $expected
     */
    public function testEnsureNonNegativeNumberAcceptsValidValues($value, $expected)
    {
        self::ensureNonNegativeNumber($value);

        $this->assertSame($expected, $value);
    }

    /**
     * @return array
     */
    public static function provideNonNegativeNumberValidValues()
    {
        return [
            'case 1' => [0, 0],
            'case 2' => [1, 1],
            'case 3' => [0.5, 0.5],
        ];
    }

    /**
     * @dataProvider provideNonNegativeNumberInvalidValues
     * @param mixed $value
     */
    public function testEnsureNonNegativeNumberRejectsInvalidValues($value)
    {
        $this->expectException(UnexpectedValueException::class);

        self::ensureNonNegativeNumber($value);
    }

    /**
     * @return array
     */
    public static function provideNonNegativeNumberInvalidValues()
    {
        return [
            'case 1' => [-1],
            'case 2' => [INF],
            'case 3' => [NAN],
            'case 4' => ['1'],
            'case 5' => [false],
        ];
    }

    /**
     * @dataProvider provideNullableNonNegativeNumberValidValues
     * @param mixed $value
     * @param mixed $expected
     */
    public function testEnsureNullableNonNegativeNumberAcceptsValidValues($value, $expected)
    {
        self::ensureNullableNonNegativeNumber($value);

        $this->assertSame($expected, $value);
    }

    /**
     * @return array
     */
    public static function provideNullableNonNegativeNumberValidValues()
    {
        return [
            'case 1' => [null, null],
            'case 2' => [0, 0],
            'case 3' => [0.5, 0.5],
        ];
    }

    /**
     * @dataProvider provideNullableNonNegativeNumberInvalidValues
     * @param mixed $value
     */
    public function testEnsureNullableNonNegativeNumberRejectsInvalidValues($value)
    {
        $this->expectException(UnexpectedValueException::class);

        self::ensureNullableNonNegativeNumber($value);
    }

    /**
     * @return array
     */
    public static function provideNullableNonNegativeNumberInvalidValues()
    {
        return [
            'case 1' => [-1],
            'case 2' => [INF],
            'case 3' => [NAN],
            'case 4' => ['1'],
        ];
    }

    /**
     * @dataProvider provideArrayValidValues
     * @param mixed $value
     * @param mixed $expected
     */
    public function testEnsureArrayAcceptsValidValues($value, $expected)
    {
        self::ensureArray($value);

        $this->assertSame($expected, $value);
    }

    /**
     * @return array
     */
    public static function provideArrayValidValues()
    {
        return [
            'case 1' => [[], []],
            'case 2' => [['key' => 1], ['key' => 1]],
        ];
    }

    /**
     * @dataProvider provideArrayInvalidValues
     * @param mixed $value
     */
    public function testEnsureArrayRejectsInvalidValues($value)
    {
        $this->expectException(UnexpectedValueException::class);

        self::ensureArray($value);
    }

    /**
     * @return array
     */
    public static function provideArrayInvalidValues()
    {
        return [
            'case 1' => [null],
            'case 2' => ['array'],
            'case 3' => [new stdClass()],
        ];
    }

    /**
     * @dataProvider provideNullableArrayValidValues
     * @param mixed $value
     * @param mixed $expected
     */
    public function testEnsureNullableArrayAcceptsValidValues($value, $expected)
    {
        self::ensureNullableArray($value);

        $this->assertSame($expected, $value);
    }

    /**
     * @return array
     */
    public static function provideNullableArrayValidValues()
    {
        return [
            'case 1' => [null, null],
            'case 2' => [[], []],
            'case 3' => [['key' => 1], ['key' => 1]],
        ];
    }

    /**
     * @dataProvider provideNullableArrayInvalidValues
     * @param mixed $value
     */
    public function testEnsureNullableArrayRejectsInvalidValues($value)
    {
        $this->expectException(UnexpectedValueException::class);

        self::ensureNullableArray($value);
    }

    /**
     * @return array
     */
    public static function provideNullableArrayInvalidValues()
    {
        return [
            'case 1' => [false],
            'case 2' => ['array'],
            'case 3' => [new stdClass()],
        ];
    }

    /**
     * @dataProvider provideNonEmptyArrayValidValues
     * @param mixed $value
     * @param mixed $expected
     */
    public function testEnsureNonEmptyArrayAcceptsValidValues($value, $expected)
    {
        self::ensureNonEmptyArray($value);

        $this->assertSame($expected, $value);
    }

    /**
     * @return array
     */
    public static function provideNonEmptyArrayValidValues()
    {
        return [
            'case 1' => [['key' => null], ['key' => null]],
            'case 2' => [[1], [1]],
        ];
    }

    /**
     * @dataProvider provideNonEmptyArrayInvalidValues
     * @param mixed $value
     */
    public function testEnsureNonEmptyArrayRejectsInvalidValues($value)
    {
        $this->expectException(UnexpectedValueException::class);

        self::ensureNonEmptyArray($value);
    }

    /**
     * @return array
     */
    public static function provideNonEmptyArrayInvalidValues()
    {
        return [
            'case 1' => [[]],
            'case 2' => [null],
            'case 3' => ['array'],
        ];
    }

    /**
     * @dataProvider provideArrayHasKeyValidValues
     * @param mixed $value
     * @param mixed $key
     * @param mixed $expected
     */
    public function testEnsureArrayHasKeyAcceptsValidValues($value, $key, $expected)
    {
        self::ensureArrayHasKey($value, $key);

        $this->assertSame($expected, $value);
    }

    /**
     * @return array
     */
    public static function provideArrayHasKeyValidValues()
    {
        return [
            'case 1' => [['key' => null], 'key', ['key' => null]],
            'case 2' => [[0 => false], 0, [0 => false]],
        ];
    }

    /**
     * @dataProvider provideArrayHasKeyInvalidValues
     * @param mixed $value
     * @param mixed $key
     */
    public function testEnsureArrayHasKeyRejectsInvalidValues($value, $key)
    {
        $this->expectException(UnexpectedValueException::class);

        self::ensureArrayHasKey($value, $key);
    }

    /**
     * @return array
     */
    public static function provideArrayHasKeyInvalidValues()
    {
        return [
            'case 1' => [[], 'key'],
            'case 2' => [['other' => 1], 'key'],
        ];
    }

    /**
     * @dataProvider provideStringArrayValidValues
     * @param mixed $value
     * @param mixed $expected
     */
    public function testEnsureStringArrayAcceptsValidValues($value, $expected)
    {
        self::ensureStringArray($value);

        $this->assertSame($expected, $value);
    }

    /**
     * @return array
     */
    public static function provideStringArrayValidValues()
    {
        return [
            'case 1' => [[], []],
            'case 2' => [[2 => 'a', 8 => 'b'], [2 => 'a', 8 => 'b']],
            'case 3' => [['key' => 'value'], ['key' => 'value']],
        ];
    }

    /**
     * @dataProvider provideStringArrayInvalidValues
     * @param mixed $value
     */
    public function testEnsureStringArrayRejectsInvalidValues($value)
    {
        $this->expectException(UnexpectedValueException::class);

        self::ensureStringArray($value);
    }

    /**
     * @return array
     */
    public static function provideStringArrayInvalidValues()
    {
        return [
            'case 1' => [['a', false]],
            'case 2' => [false],
            'case 3' => [[1]],
        ];
    }

    /**
     * @dataProvider provideNullableStringArrayValidValues
     * @param mixed $value
     * @param mixed $expected
     */
    public function testEnsureNullableStringArrayAcceptsValidValues($value, $expected)
    {
        self::ensureNullableStringArray($value);

        $this->assertSame($expected, $value);
    }

    /**
     * @return array
     */
    public static function provideNullableStringArrayValidValues()
    {
        return [
            'case 1' => [null, null],
            'case 2' => [[], []],
            'case 3' => [[2 => 'a'], [2 => 'a']],
        ];
    }

    /**
     * @dataProvider provideNullableStringArrayInvalidValues
     * @param mixed $value
     */
    public function testEnsureNullableStringArrayRejectsInvalidValues($value)
    {
        $this->expectException(UnexpectedValueException::class);

        self::ensureNullableStringArray($value);
    }

    /**
     * @return array
     */
    public static function provideNullableStringArrayInvalidValues()
    {
        return [
            'case 1' => [['a', false]],
            'case 2' => [false],
        ];
    }

    /**
     * @dataProvider provideNullableStringOrStringArrayValidValues
     * @param mixed $value
     * @param mixed $expected
     */
    public function testEnsureNullableStringOrStringArrayAcceptsValidValues($value, $expected)
    {
        self::ensureNullableStringOrStringArray($value);

        $this->assertSame($expected, $value);
    }

    /**
     * @return array
     */
    public static function provideNullableStringOrStringArrayValidValues()
    {
        return [
            'case 1' => [null, null],
            'case 2' => ['echo', 'echo'],
            'case 3' => [[2 => 'a', 8 => 'b'], [2 => 'a', 8 => 'b']],
        ];
    }

    /**
     * @dataProvider provideNullableStringOrStringArrayInvalidValues
     * @param mixed $value
     */
    public function testEnsureNullableStringOrStringArrayRejectsInvalidValues($value)
    {
        $this->expectException(UnexpectedValueException::class);

        self::ensureNullableStringOrStringArray($value);
    }

    /**
     * @return array
     */
    public static function provideNullableStringOrStringArrayInvalidValues()
    {
        return [
            'case 1' => [['a', false]],
            'case 2' => [false],
            'case 3' => [1],
        ];
    }

    /**
     * @dataProvider provideStringOrArrayValidValues
     * @param mixed $value
     * @param mixed $expected
     */
    public function testEnsureStringOrArrayAcceptsValidValues($value, $expected)
    {
        self::ensureStringOrArray($value);

        $this->assertSame($expected, $value);
    }

    /**
     * @return array
     */
    public static function provideStringOrArrayValidValues()
    {
        return [
            'case 1' => ['', ''],
            'case 2' => [[], []],
            'case 3' => [['key' => null], ['key' => null]],
        ];
    }

    /**
     * @dataProvider provideStringOrArrayInvalidValues
     * @param mixed $value
     */
    public function testEnsureStringOrArrayRejectsInvalidValues($value)
    {
        $this->expectException(UnexpectedValueException::class);

        self::ensureStringOrArray($value);
    }

    /**
     * @return array
     */
    public static function provideStringOrArrayInvalidValues()
    {
        return [
            'case 1' => [null],
            'case 2' => [false],
            'case 3' => [new stdClass()],
        ];
    }

    /**
     * @dataProvider provideStringKeyedArrayValidValues
     * @param mixed $value
     * @param mixed $expected
     */
    public function testEnsureStringKeyedArrayAcceptsValidValues($value, $expected)
    {
        self::ensureStringKeyedArray($value);

        $this->assertSame($expected, $value);
    }

    /**
     * @return array
     */
    public static function provideStringKeyedArrayValidValues()
    {
        $value2 = ['key' => new stdClass()];

        return [
            'case 1' => [[], []],
            'case 2' => [['key' => null], ['key' => null]],
            'case 3' => [$value2, $value2],
        ];
    }

    /**
     * @dataProvider provideStringKeyedArrayInvalidValues
     * @param mixed $value
     */
    public function testEnsureStringKeyedArrayRejectsInvalidValues($value)
    {
        $this->expectException(UnexpectedValueException::class);

        self::ensureStringKeyedArray($value);
    }

    /**
     * @return array
     */
    public static function provideStringKeyedArrayInvalidValues()
    {
        return [
            'case 1' => [[0 => 1]],
            'case 2' => [false],
        ];
    }

    /**
     * @dataProvider provideStringMapValidValues
     * @param mixed $value
     * @param mixed $expected
     */
    public function testEnsureStringMapAcceptsValidValues($value, $expected)
    {
        self::ensureStringMap($value);

        $this->assertSame($expected, $value);
    }

    /**
     * @return array
     */
    public static function provideStringMapValidValues()
    {
        return [
            'case 1' => [[], []],
            'case 2' => [['key' => ''], ['key' => '']],
            'case 3' => [['key' => 'value'], ['key' => 'value']],
        ];
    }

    /**
     * @dataProvider provideStringMapInvalidValues
     * @param mixed $value
     */
    public function testEnsureStringMapRejectsInvalidValues($value)
    {
        $this->expectException(UnexpectedValueException::class);

        self::ensureStringMap($value);
    }

    /**
     * @return array
     */
    public static function provideStringMapInvalidValues()
    {
        return [
            'case 1' => [['key' => false]],
            'case 2' => [[0 => 'value']],
            'case 3' => [false],
        ];
    }

    /**
     * @dataProvider provideNullableStringMapValidValues
     * @param mixed $value
     * @param mixed $expected
     */
    public function testEnsureNullableStringMapAcceptsValidValues($value, $expected)
    {
        self::ensureNullableStringMap($value);

        $this->assertSame($expected, $value);
    }

    /**
     * @return array
     */
    public static function provideNullableStringMapValidValues()
    {
        return [
            'case 1' => [null, null],
            'case 2' => [[], []],
            'case 3' => [['key' => 'value'], ['key' => 'value']],
        ];
    }

    /**
     * @dataProvider provideNullableStringMapInvalidValues
     * @param mixed $value
     */
    public function testEnsureNullableStringMapRejectsInvalidValues($value)
    {
        $this->expectException(UnexpectedValueException::class);

        self::ensureNullableStringMap($value);
    }

    /**
     * @return array
     */
    public static function provideNullableStringMapInvalidValues()
    {
        return [
            'case 1' => [['key' => false]],
            'case 2' => [false],
        ];
    }

    /**
     * @dataProvider provideNullableBoolOrStringValidValues
     * @param mixed $value
     * @param mixed $expected
     */
    public function testEnsureNullableBoolOrStringAcceptsValidValues($value, $expected)
    {
        self::ensureNullableBoolOrString($value);

        $this->assertSame($expected, $value);
    }

    /**
     * @return array
     */
    public static function provideNullableBoolOrStringValidValues()
    {
        return [
            'case 1' => [null, null],
            'case 2' => [true, true],
            'case 3' => [false, false],
            'case 4' => ['value', 'value'],
        ];
    }

    /**
     * @dataProvider provideNullableBoolOrStringInvalidValues
     * @param mixed $value
     */
    public function testEnsureNullableBoolOrStringRejectsInvalidValues($value)
    {
        $this->expectException(UnexpectedValueException::class);

        self::ensureNullableBoolOrString($value);
    }

    /**
     * @return array
     */
    public static function provideNullableBoolOrStringInvalidValues()
    {
        return [
            'case 1' => [1],
            'case 2' => [[]],
            'case 3' => [new stdClass()],
        ];
    }

    /**
     * @dataProvider provideObjectValidValues
     * @param mixed $value
     * @param mixed $expected
     */
    public function testEnsureObjectAcceptsValidValues($value, $expected)
    {
        self::ensureObject($value);

        $this->assertSame($expected, $value);
    }

    /**
     * @return array
     */
    public static function provideObjectValidValues()
    {
        $value0 = new stdClass();

        return [
            'case 1' => [$value0, $value0],
        ];
    }

    /**
     * @dataProvider provideObjectInvalidValues
     * @param mixed $value
     */
    public function testEnsureObjectRejectsInvalidValues($value)
    {
        $this->expectException(UnexpectedValueException::class);

        self::ensureObject($value);
    }

    /**
     * @return array
     */
    public static function provideObjectInvalidValues()
    {
        return [
            'case 1' => [null],
            'case 2' => [[]],
            'case 3' => ['object'],
        ];
    }

    /**
     * @dataProvider provideClassStringValidValues
     * @param mixed $value
     * @param mixed $expected
     */
    public function testEnsureClassStringAcceptsValidValues($value, $expected)
    {
        self::ensureClassString($value);

        $this->assertSame($expected, $value);
    }

    /**
     * @return array
     */
    public static function provideClassStringValidValues()
    {
        return [
            'case 1' => [stdClass::class, stdClass::class],
            'case 2' => [Traversable::class, Traversable::class],
        ];
    }

    /**
     * @dataProvider provideClassStringInvalidValues
     * @param mixed $value
     */
    public function testEnsureClassStringRejectsInvalidValues($value)
    {
        $this->expectException(UnexpectedValueException::class);

        self::ensureClassString($value);
    }

    /**
     * @return array
     */
    public static function provideClassStringInvalidValues()
    {
        return [
            'case 1' => ['ClassThatDoesNotExist'],
            'case 2' => [false],
            'case 3' => [new stdClass()],
        ];
    }

    /**
     * @dataProvider provideInstanceOfValidValues
     * @param mixed $value
     * @param mixed $class
     * @param mixed $expected
     */
    public function testEnsureInstanceOfAcceptsValidValues($value, $class, $expected)
    {
        self::ensureInstanceOf($value, $class);

        $this->assertSame($expected, $value);
    }

    /**
     * @return array
     */
    public static function provideInstanceOfValidValues()
    {
        $value0 = new stdClass();

        return [
            'case 1' => [$value0, stdClass::class, $value0],
        ];
    }

    /**
     * @dataProvider provideInstanceOfInvalidValues
     * @param mixed $value
     * @param mixed $class
     */
    public function testEnsureInstanceOfRejectsInvalidValues($value, $class)
    {
        $this->expectException(UnexpectedValueException::class);

        self::ensureInstanceOf($value, $class);
    }

    /**
     * @return array
     */
    public static function provideInstanceOfInvalidValues()
    {
        return [
            'case 1' => [null, stdClass::class],
            'case 2' => [[], stdClass::class],
            'case 3' => [new stdClass(), Traversable::class],
        ];
    }

    /**
     * @dataProvider provideNullableInstanceOfValidValues
     * @param mixed $value
     * @param mixed $class
     * @param mixed $expected
     */
    public function testEnsureNullableInstanceOfAcceptsValidValues($value, $class, $expected)
    {
        self::ensureNullableInstanceOf($value, $class);

        $this->assertSame($expected, $value);
    }

    /**
     * @return array
     */
    public static function provideNullableInstanceOfValidValues()
    {
        $value1 = new stdClass();

        return [
            'case 1' => [null, stdClass::class, null],
            'case 2' => [$value1, stdClass::class, $value1],
        ];
    }

    /**
     * @dataProvider provideNullableInstanceOfInvalidValues
     * @param mixed $value
     * @param mixed $class
     */
    public function testEnsureNullableInstanceOfRejectsInvalidValues($value, $class)
    {
        $this->expectException(UnexpectedValueException::class);

        self::ensureNullableInstanceOf($value, $class);
    }

    /**
     * @return array
     */
    public static function provideNullableInstanceOfInvalidValues()
    {
        return [
            'case 1' => [[], stdClass::class],
            'case 2' => [false, stdClass::class],
        ];
    }

    /**
     * @dataProvider provideNonEmptyStringOrInstanceOfValidValues
     * @param mixed $value
     * @param mixed $class
     * @param mixed $expected
     */
    public function testEnsureNonEmptyStringOrInstanceOfAcceptsValidValues($value, $class, $expected)
    {
        self::ensureNonEmptyStringOrInstanceOf($value, $class);

        $this->assertSame($expected, $value);
    }

    /**
     * @return array
     */
    public static function provideNonEmptyStringOrInstanceOfValidValues()
    {
        $value1 = new stdClass();

        return [
            'case 1' => ['value', stdClass::class, 'value'],
            'case 2' => [$value1, stdClass::class, $value1],
        ];
    }

    /**
     * @dataProvider provideNonEmptyStringOrInstanceOfInvalidValues
     * @param mixed $value
     * @param mixed $class
     */
    public function testEnsureNonEmptyStringOrInstanceOfRejectsInvalidValues($value, $class)
    {
        $this->expectException(UnexpectedValueException::class);

        self::ensureNonEmptyStringOrInstanceOf($value, $class);
    }

    /**
     * @return array
     */
    public static function provideNonEmptyStringOrInstanceOfInvalidValues()
    {
        return [
            'case 1' => ['', stdClass::class],
            'case 2' => [null, stdClass::class],
            'case 3' => [false, stdClass::class],
        ];
    }

    /**
     * @dataProvider provideStringOrArrayOrInstanceOfValidValues
     * @param mixed $value
     * @param mixed $class
     * @param mixed $expected
     */
    public function testEnsureStringOrArrayOrInstanceOfAcceptsValidValues($value, $class, $expected)
    {
        self::ensureStringOrArrayOrInstanceOf($value, $class);

        $this->assertSame($expected, $value);
    }

    /**
     * @return array
     */
    public static function provideStringOrArrayOrInstanceOfValidValues()
    {
        $value2 = new stdClass();

        return [
            'case 1' => ['value', stdClass::class, 'value'],
            'case 2' => [[], stdClass::class, []],
            'case 3' => [$value2, stdClass::class, $value2],
        ];
    }

    /**
     * @dataProvider provideStringOrArrayOrInstanceOfInvalidValues
     * @param mixed $value
     * @param mixed $class
     */
    public function testEnsureStringOrArrayOrInstanceOfRejectsInvalidValues($value, $class)
    {
        $this->expectException(UnexpectedValueException::class);

        self::ensureStringOrArrayOrInstanceOf($value, $class);
    }

    /**
     * @return array
     */
    public static function provideStringOrArrayOrInstanceOfInvalidValues()
    {
        return [
            'case 1' => [false, stdClass::class],
            'case 2' => [null, stdClass::class],
        ];
    }

    /**
     * @dataProvider provideNullValidValues
     * @param mixed $value
     * @param mixed $expected
     */
    public function testEnsureNullAcceptsValidValues($value, $expected)
    {
        self::ensureNull($value);

        $this->assertSame($expected, $value);
    }

    /**
     * @return array
     */
    public static function provideNullValidValues()
    {
        return [
            'case 1' => [null, null],
        ];
    }

    /**
     * @dataProvider provideNullInvalidValues
     * @param mixed $value
     */
    public function testEnsureNullRejectsInvalidValues($value)
    {
        $this->expectException(UnexpectedValueException::class);

        self::ensureNull($value);
    }

    /**
     * @return array
     */
    public static function provideNullInvalidValues()
    {
        return [
            'case 1' => [false],
            'case 2' => [0],
            'case 3' => [''],
            'case 4' => [[]],
        ];
    }

    /**
     * @dataProvider provideOneOfValidValues
     * @param mixed $value
     * @param mixed $values
     * @param mixed $expected
     */
    public function testEnsureOneOfAcceptsValidValues($value, $values, $expected)
    {
        self::ensureOneOf($value, $values);

        $this->assertSame($expected, $value);
    }

    /**
     * @return array
     */
    public static function provideOneOfValidValues()
    {
        return [
            'case 1' => ['one', ['one', 'two'], 'one'],
        ];
    }

    /**
     * @dataProvider provideOneOfInvalidValues
     * @param mixed $value
     * @param mixed $values
     */
    public function testEnsureOneOfRejectsInvalidValues($value, $values)
    {
        $this->expectException(InvalidFormatException::class);

        self::ensureOneOf($value, $values);
    }

    /**
     * @return array
     */
    public static function provideOneOfInvalidValues()
    {
        return [
            'case 1' => ['three', ['one', 'two']],
            'case 2' => [1, ['1']],
        ];
    }

    /**
     * @dataProvider provideMatchesValidValues
     * @param mixed $value
     * @param mixed $pattern
     * @param mixed $expected
     */
    public function testEnsureMatchesAcceptsValidValues($value, $pattern, $expected)
    {
        self::ensureMatches($value, $pattern);

        $this->assertSame($expected, $value);
    }

    /**
     * @return array
     */
    public static function provideMatchesValidValues()
    {
        return [
            'case 1' => ['123', '/^[0-9]+$/', '123'],
        ];
    }

    /**
     * @dataProvider provideMatchesInvalidValues
     * @param mixed $value
     * @param mixed $pattern
     */
    public function testEnsureMatchesRejectsInvalidValues($value, $pattern)
    {
        $this->expectException(InvalidFormatException::class);

        self::ensureMatches($value, $pattern);
    }

    /**
     * @return array
     */
    public static function provideMatchesInvalidValues()
    {
        return [
            'case 1' => ['123x', '/^[0-9]+$/'],
        ];
    }

    /**
     * @dataProvider provideNullableStreamValidValues
     * @param mixed $value
     * @param mixed $expected
     */
    public function testEnsureNullableStreamAcceptsValidValues($value, $expected)
    {
        self::ensureNullableStream($value);

        $this->assertSame($expected, $value);
    }

    /**
     * @return array
     */
    public static function provideNullableStreamValidValues()
    {
        $value1 = fopen('php://memory', 'w+');

        return [
            'case 1' => [null, null],
            'case 2' => [$value1, $value1],
        ];
    }

    /**
     * @dataProvider provideNullableStreamInvalidValues
     * @param mixed $value
     */
    public function testEnsureNullableStreamRejectsInvalidValues($value)
    {
        $this->expectException(UnexpectedValueException::class);

        self::ensureNullableStream($value);
    }

    /**
     * @return array
     */
    public static function provideNullableStreamInvalidValues()
    {
        return [
            'case 1' => [false],
            'case 2' => ['stream'],
            'case 3' => [new stdClass()],
        ];
    }

    /**
     * @dataProvider provideStreamValidValues
     * @param mixed $value
     * @param mixed $expected
     */
    public function testEnsureStreamAcceptsValidValues($value, $expected)
    {
        self::ensureStream($value);

        $this->assertSame($expected, $value);
    }

    /**
     * @return array
     */
    public static function provideStreamValidValues()
    {
        $value0 = fopen('php://memory', 'w+');

        return [
            'case 1' => [$value0, $value0],
        ];
    }

    /**
     * @dataProvider provideStreamInvalidValues
     * @param mixed $value
     */
    public function testEnsureStreamRejectsInvalidValues($value)
    {
        $this->expectException(UnexpectedValueException::class);

        self::ensureStream($value);
    }

    /**
     * @return array
     */
    public static function provideStreamInvalidValues()
    {
        return [
            'case 1' => [null],
            'case 2' => ['php://memory'],
            'case 3' => [false],
            'case 4' => [new stdClass()],
        ];
    }

    /**
     * @dataProvider provideNullableScalarOrStreamOrTraversableValidValues
     * @param mixed $value
     * @param mixed $expected
     */
    public function testEnsureNullableScalarOrStreamOrTraversableAcceptsValidValues($value, $expected)
    {
        self::ensureNullableScalarOrStreamOrTraversable($value);

        $this->assertSame($expected, $value);
    }

    /**
     * @return array
     */
    public static function provideNullableScalarOrStreamOrTraversableValidValues()
    {
        $value4 = fopen('php://memory', 'w+');
        $value5 = new ArrayIterator([]);

        return [
            'case 1' => [null, null],
            'case 2' => ['input', 'input'],
            'case 3' => [0, 0],
            'case 4' => [false, false],
            'case 5' => [$value4, $value4],
            'case 6' => [$value5, $value5],
        ];
    }

    /**
     * @dataProvider provideNullableScalarOrStreamOrTraversableInvalidValues
     * @param mixed $value
     */
    public function testEnsureNullableScalarOrStreamOrTraversableRejectsInvalidValues($value)
    {
        $this->expectException(UnexpectedValueException::class);

        self::ensureNullableScalarOrStreamOrTraversable($value);
    }

    /**
     * @return array
     */
    public static function provideNullableScalarOrStreamOrTraversableInvalidValues()
    {
        return [
            'case 1' => [[]],
            'case 2' => [new stdClass()],
        ];
    }

    /**
     * @dataProvider provideEmptyArrayValidValues
     * @param mixed $value
     * @param mixed $expected
     */
    public function testEnsureEmptyArrayAcceptsValidValues($value, $expected)
    {
        self::ensureEmptyArray($value);

        $this->assertSame($expected, $value);
    }

    /**
     * @return array
     */
    public static function provideEmptyArrayValidValues()
    {
        return [
            'case 1' => [[], []],
        ];
    }

    /**
     * @dataProvider provideEmptyArrayInvalidValues
     * @param mixed $value
     */
    public function testEnsureEmptyArrayRejectsInvalidValues($value)
    {
        $this->expectException(UnexpectedValueException::class);

        self::ensureEmptyArray($value);
    }

    /**
     * @return array
     */
    public static function provideEmptyArrayInvalidValues()
    {
        return [
            'case 1' => [null],
            'case 2' => [false],
            'case 3' => [['value']],
        ];
    }

    /**
     * @dataProvider provideClassStringOfValidValues
     * @param mixed $value
     * @param mixed $class
     * @param mixed $expected
     */
    public function testEnsureClassStringOfAcceptsValidValues($value, $class, $expected)
    {
        self::ensureClassStringOf($value, $class);

        $this->assertSame($expected, $value);
    }

    /**
     * @return array
     */
    public static function provideClassStringOfValidValues()
    {
        return [
            'case 1' => [ArrayIterator::class, Traversable::class, ArrayIterator::class],
        ];
    }

    /**
     * @dataProvider provideClassStringOfInvalidValues
     * @param mixed $value
     * @param mixed $class
     */
    public function testEnsureClassStringOfRejectsInvalidValues($value, $class)
    {
        $this->expectException(UnexpectedValueException::class);

        self::ensureClassStringOf($value, $class);
    }

    /**
     * @return array
     */
    public static function provideClassStringOfInvalidValues()
    {
        return [
            'case 1' => [stdClass::class, Traversable::class],
            'case 2' => ['MissingClass', Traversable::class],
            'case 3' => [false, Traversable::class],
        ];
    }

    /**
     * @dataProvider provideClassStringOrInstanceOfValidValues
     * @param mixed $value
     * @param mixed $class
     * @param mixed $expected
     */
    public function testEnsureClassStringOrInstanceOfAcceptsValidValues($value, $class, $expected)
    {
        self::ensureClassStringOrInstanceOf($value, $class);

        $this->assertSame($expected, $value);
    }

    /**
     * @return array
     */
    public static function provideClassStringOrInstanceOfValidValues()
    {
        $value1 = new ArrayIterator([]);

        return [
            'case 1' => [ArrayIterator::class, Traversable::class, ArrayIterator::class],
            'case 2' => [$value1, Traversable::class, $value1],
        ];
    }

    /**
     * @dataProvider provideClassStringOrInstanceOfInvalidValues
     * @param mixed $value
     * @param mixed $class
     */
    public function testEnsureClassStringOrInstanceOfRejectsInvalidValues($value, $class)
    {
        $this->expectException(UnexpectedValueException::class);

        self::ensureClassStringOrInstanceOf($value, $class);
    }

    /**
     * @return array
     */
    public static function provideClassStringOrInstanceOfInvalidValues()
    {
        return [
            'case 1' => [stdClass::class, Traversable::class],
            'case 2' => [new stdClass(), Traversable::class],
            'case 3' => [false, Traversable::class],
        ];
    }

    /**
     * @dataProvider provideScalarOrStringableValidValues
     * @param mixed $value
     * @param mixed $expected
     */
    public function testEnsureScalarOrStringableAcceptsValidValues($value, $expected)
    {
        self::ensureScalarOrStringable($value);

        $this->assertSame($expected, $value);
    }

    /**
     * @return array
     */
    public static function provideScalarOrStringableValidValues()
    {
        $value3 = new SimpleXMLElement('<value>text</value>');

        return [
            'case 1' => ['text', 'text'],
            'case 2' => [1, 1],
            'case 3' => [false, false],
            'case 4' => [$value3, $value3],
        ];
    }

    /**
     * @dataProvider provideScalarOrStringableInvalidValues
     * @param mixed $value
     */
    public function testEnsureScalarOrStringableRejectsInvalidValues($value)
    {
        $this->expectException(UnexpectedValueException::class);

        self::ensureScalarOrStringable($value);
    }

    /**
     * @return array
     */
    public static function provideScalarOrStringableInvalidValues()
    {
        return [
            'case 1' => [null],
            'case 2' => [[]],
            'case 3' => [new stdClass()],
        ];
    }

    /**
     * @dataProvider provideNullableScalarOrStringableOrArrayValidValues
     * @param mixed $value
     * @param mixed $expected
     */
    public function testEnsureNullableScalarOrStringableOrArrayAcceptsValidValues($value, $expected)
    {
        self::ensureNullableScalarOrStringableOrArray($value);

        $this->assertSame($expected, $value);
    }

    /**
     * @return array
     */
    public static function provideNullableScalarOrStringableOrArrayValidValues()
    {
        $value3 = [new SimpleXMLElement('<value>text</value>')];

        return [
            'case 1' => [null, null],
            'case 2' => [false, false],
            'case 3' => [['one', 2], ['one', 2]],
            'case 4' => [$value3, $value3],
        ];
    }

    /**
     * @dataProvider provideNullableScalarOrStringableOrArrayInvalidValues
     * @param mixed $value
     */
    public function testEnsureNullableScalarOrStringableOrArrayRejectsInvalidValues($value)
    {
        $this->expectException(UnexpectedValueException::class);

        self::ensureNullableScalarOrStringableOrArray($value);
    }

    /**
     * @return array
     */
    public static function provideNullableScalarOrStringableOrArrayInvalidValues()
    {
        return [
            'case 1' => [new stdClass()],
            'case 2' => [[[1]]],
            'case 3' => [['bad' => new stdClass()]],
        ];
    }

    public function testEnsureStringUsesProvidedMessage()
    {
        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('A custom validation message');

        self::ensureString(false, 'A custom validation message');
    }

    /**
     * @dataProvider provideStringMapInvalidValues
     * @param mixed $value
     */
    public function testEnsureStringMapUsesProvidedMessage($value)
    {
        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('A custom validation message');

        self::ensureStringMap($value, 'A custom validation message');
    }
}
