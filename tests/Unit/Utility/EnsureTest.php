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
final class EnsureTest extends TestCase
{
    use Ensure;

    /**
     * @dataProvider providerNullableScalarOrStringableOrArrayMapValidValues
     * @param array<string, null|scalar|SimpleXMLElement|array<array-key, scalar|SimpleXMLElement>> $value
     * @return void
     */
    public function testEnsureNullableScalarOrStringableOrArrayMapReturnsExpectedValue($value)
    {
        $actual = self::ensureNullableScalarOrStringableOrArrayMap($value);

        $this->assertSame($value, $actual);
    }

    /**
     * @return array<string, array{array<string, null|scalar|SimpleXMLElement|array<array-key, scalar|SimpleXMLElement>>}>
     */
    public static function providerNullableScalarOrStringableOrArrayMapValidValues()
    {
        return [
            'empty map' => [[]],
            'scalar values' => [['null' => null, 'false' => false, 'true' => true, 'int' => 1, 'float' => 1.5, 'string' => 'value']],
            'stringable value' => [['value' => new SimpleXMLElement('<value>text</value>')]],
            'array values' => [['list' => ['first', 2], 'map' => ['key' => 'value'], 'empty' => []]],
            'stringable array' => [['values' => [new SimpleXMLElement('<value>text</value>')]]],
        ];
    }

    /**
     * @dataProvider providerNullableScalarOrStringableOrArrayMapInvalidValues
     * @param null|bool|array<array-key, string|stdClass|array<array-key, null|array{}>> $value
     * @return void
     */
    public function testEnsureNullableScalarOrStringableOrArrayMapRejectsInvalidValues($value)
    {
        $this->expectException(UnexpectedValueException::class);

        self::ensureNullableScalarOrStringableOrArrayMap($value);
    }

    /**
     * @return array<string, array{null|bool|array<array-key, string|stdClass|array<array-key, null|array{}>>}>
     */
    public static function providerNullableScalarOrStringableOrArrayMapInvalidValues()
    {
        return [
            'null map' => [null],
            'boolean map' => [false],
            'integer key' => [['value']],
            'unrelated object' => [['value' => new stdClass()]],
            'nested array' => [['values' => [[]]]],
            'null array item' => [['values' => [null]]],
        ];
    }

    /**
     * @return void
     */
    public function testEnsureNullableScalarOrStringableOrArrayMapUsesProvidedMessage()
    {
        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('Invalid input');

        self::ensureNullableScalarOrStringableOrArrayMap(['value' => new stdClass()], 'Invalid input');
    }

    /**
     * @dataProvider providerStringValidValues
     * @param string $value
     * @param string $expected
     * @return void
     */
    public function testEnsureStringReturnsExpectedValue($value, $expected)
    {
        $actual = self::ensureString($value);

        $this->assertSame($expected, $actual);
    }

    /**
     * @return array<string, array{string, string}>
     */
    public static function providerStringValidValues()
    {
        return [
            'case 1' => ['', ''],
            'case 2' => ['text', 'text'],
            'case 3' => ['0', '0'],
        ];
    }

    /**
     * @dataProvider providerStringInvalidValues
     * @param array{}|bool|int|null|stdClass $value
     * @return void
     */
    public function testEnsureStringRejectsInvalidValues($value)
    {
        $this->expectException(UnexpectedValueException::class);

        self::ensureString($value);
    }

    /**
     * @return array<string, array{array{}|bool|int|null|stdClass}>
     */
    public static function providerStringInvalidValues()
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
     * @dataProvider providerNonEmptyStringValidValues
     * @param string $value
     * @param string $expected
     * @return void
     */
    public function testEnsureNonEmptyStringReturnsExpectedValue($value, $expected)
    {
        $actual = self::ensureNonEmptyString($value);

        $this->assertSame($expected, $actual);
    }

    /**
     * @return array<string, array{string, string}>
     */
    public static function providerNonEmptyStringValidValues()
    {
        return [
            'case 1' => ['text', 'text'],
            'case 2' => ['0', '0'],
        ];
    }

    /**
     * @dataProvider providerNonEmptyStringInvalidValues
     * @param bool|int|null|string $value
     * @return void
     */
    public function testEnsureNonEmptyStringRejectsInvalidValues($value)
    {
        $this->expectException(UnexpectedValueException::class);

        self::ensureNonEmptyString($value);
    }

    /**
     * @return array<string, array{bool|int|null|string}>
     */
    public static function providerNonEmptyStringInvalidValues()
    {
        return [
            'case 1' => [''],
            'case 2' => [null],
            'case 3' => [false],
            'case 4' => [1],
        ];
    }

    /**
     * @dataProvider providerNullableStringValidValues
     * @param null|string $value
     * @param null|string $expected
     * @return void
     */
    public function testEnsureNullableStringReturnsExpectedValue($value, $expected)
    {
        $actual = self::ensureNullableString($value);

        $this->assertSame($expected, $actual);
    }

    /**
     * @return array<string, array{null|string, null|string}>
     */
    public static function providerNullableStringValidValues()
    {
        return [
            'case 1' => [null, null],
            'case 2' => ['', ''],
            'case 3' => ['text', 'text'],
        ];
    }

    /**
     * @dataProvider providerNullableStringInvalidValues
     * @param array{}|bool|int $value
     * @return void
     */
    public function testEnsureNullableStringRejectsInvalidValues($value)
    {
        $this->expectException(UnexpectedValueException::class);

        self::ensureNullableString($value);
    }

    /**
     * @return array<string, array{array{}|bool|int}>
     */
    public static function providerNullableStringInvalidValues()
    {
        return [
            'case 1' => [false],
            'case 2' => [1],
            'case 3' => [[]],
        ];
    }

    /**
     * @dataProvider providerNullableNonEmptyStringValidValues
     * @param null|string $value
     * @param null|string $expected
     * @return void
     */
    public function testEnsureNullableNonEmptyStringReturnsExpectedValue($value, $expected)
    {
        $actual = self::ensureNullableNonEmptyString($value);

        $this->assertSame($expected, $actual);
    }

    /**
     * @return array<string, array{null|string, null|string}>
     */
    public static function providerNullableNonEmptyStringValidValues()
    {
        return [
            'case 1' => [null, null],
            'case 2' => ['text', 'text'],
            'case 3' => ['0', '0'],
        ];
    }

    /**
     * @dataProvider providerNullableNonEmptyStringInvalidValues
     * @param bool|int|string $value
     * @return void
     */
    public function testEnsureNullableNonEmptyStringRejectsInvalidValues($value)
    {
        $this->expectException(UnexpectedValueException::class);

        self::ensureNullableNonEmptyString($value);
    }

    /**
     * @return array<string, array{bool|int|string}>
     */
    public static function providerNullableNonEmptyStringInvalidValues()
    {
        return [
            'case 1' => [''],
            'case 2' => [false],
            'case 3' => [1],
        ];
    }

    /**
     * @dataProvider providerBoolValidValues
     * @param bool $value
     * @param bool $expected
     * @return void
     */
    public function testEnsureBoolReturnsExpectedValue($value, $expected)
    {
        $actual = self::ensureBool($value);

        $this->assertSame($expected, $actual);
    }

    /**
     * @return array<string, array{bool, bool}>
     */
    public static function providerBoolValidValues()
    {
        return [
            'case 1' => [true, true],
            'case 2' => [false, false],
        ];
    }

    /**
     * @dataProvider providerBoolInvalidValues
     * @param array{}|int|null|string $value
     * @return void
     */
    public function testEnsureBoolRejectsInvalidValues($value)
    {
        $this->expectException(UnexpectedValueException::class);

        self::ensureBool($value);
    }

    /**
     * @return array<string, array{array{}|int|null|string}>
     */
    public static function providerBoolInvalidValues()
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
     * @dataProvider providerNullableBoolValidValues
     * @param bool|null $value
     * @param bool|null $expected
     * @return void
     */
    public function testEnsureNullableBoolReturnsExpectedValue($value, $expected)
    {
        $actual = self::ensureNullableBool($value);

        $this->assertSame($expected, $actual);
    }

    /**
     * @return array<string, array{bool|null, bool|null}>
     */
    public static function providerNullableBoolValidValues()
    {
        return [
            'case 1' => [null, null],
            'case 2' => [true, true],
            'case 3' => [false, false],
        ];
    }

    /**
     * @dataProvider providerNullableBoolInvalidValues
     * @param array{}|int|string $value
     * @return void
     */
    public function testEnsureNullableBoolRejectsInvalidValues($value)
    {
        $this->expectException(UnexpectedValueException::class);

        self::ensureNullableBool($value);
    }

    /**
     * @return array<string, array{array{}|int|string}>
     */
    public static function providerNullableBoolInvalidValues()
    {
        return [
            'case 1' => [0],
            'case 2' => ['false'],
            'case 3' => [[]],
        ];
    }

    /**
     * @dataProvider providerIntValidValues
     * @param int $value
     * @param int $expected
     * @return void
     */
    public function testEnsureIntReturnsExpectedValue($value, $expected)
    {
        $actual = self::ensureInt($value);

        $this->assertSame($expected, $actual);
    }

    /**
     * @return array<string, array{int, int}>
     */
    public static function providerIntValidValues()
    {
        return [
            'case 1' => [0, 0],
            'case 2' => [1, 1],
            'case 3' => [-1, -1],
        ];
    }

    /**
     * @dataProvider providerIntInvalidValues
     * @param bool|float|null|string $value
     * @return void
     */
    public function testEnsureIntRejectsInvalidValues($value)
    {
        $this->expectException(UnexpectedValueException::class);

        self::ensureInt($value);
    }

    /**
     * @return array<string, array{bool|float|null|string}>
     */
    public static function providerIntInvalidValues()
    {
        return [
            'case 1' => ['1'],
            'case 2' => [1.5],
            'case 3' => [true],
            'case 4' => [null],
        ];
    }

    /**
     * @dataProvider providerIntRangeValidValues
     * @param int      $value
     * @param int      $minimum
     * @param int|null $maximum
     * @param int      $expected
     * @return void
     */
    public function testEnsureIntRangeReturnsExpectedValue($value, $minimum, $maximum, $expected)
    {
        $actual = self::ensureIntRange($value, $minimum, $maximum);

        $this->assertSame($expected, $actual);
    }

    /**
     * @return array<string, array{int, int, int|null, int}>
     */
    public static function providerIntRangeValidValues()
    {
        return [
            'case 1' => [0, 0, 10, 0],
            'case 2' => [10, 0, 10, 10],
            'case 3' => [999, 0, null, 999],
        ];
    }

    /**
     * @dataProvider providerIntRangeInvalidValues
     * @param int|string $value
     * @param int        $minimum
     * @param int        $maximum
     * @return void
     */
    public function testEnsureIntRangeRejectsInvalidValues($value, $minimum, $maximum)
    {
        $this->expectException(UnexpectedValueException::class);

        self::ensureIntRange($value, $minimum, $maximum);
    }

    /**
     * @return array<string, array{int|string, int, int}>
     */
    public static function providerIntRangeInvalidValues()
    {
        return [
            'case 1' => [-1, 0, 10],
            'case 2' => [11, 0, 10],
            'case 3' => ['1', 0, 10],
        ];
    }

    /**
     * @dataProvider providerNullableIntRangeValidValues
     * @param int|null $value
     * @param int      $minimum
     * @param int      $maximum
     * @param int|null $expected
     * @return void
     */
    public function testEnsureNullableIntRangeReturnsExpectedValue($value, $minimum, $maximum, $expected)
    {
        $actual = self::ensureNullableIntRange($value, $minimum, $maximum);

        $this->assertSame($expected, $actual);
    }

    /**
     * @return array<string, array{int|null, int, int, int|null}>
     */
    public static function providerNullableIntRangeValidValues()
    {
        return [
            'case 1' => [null, 0, 10, null],
            'case 2' => [0, 0, 10, 0],
            'case 3' => [10, 0, 10, 10],
        ];
    }

    /**
     * @dataProvider providerNullableIntRangeInvalidValues
     * @param bool|int $value
     * @param int      $minimum
     * @param int      $maximum
     * @return void
     */
    public function testEnsureNullableIntRangeRejectsInvalidValues($value, $minimum, $maximum)
    {
        $this->expectException(UnexpectedValueException::class);

        self::ensureNullableIntRange($value, $minimum, $maximum);
    }

    /**
     * @return array<string, array{bool|int, int, int}>
     */
    public static function providerNullableIntRangeInvalidValues()
    {
        return [
            'case 1' => [-1, 0, 10],
            'case 2' => [11, 0, 10],
            'case 3' => [false, 0, 10],
        ];
    }

    /**
     * @dataProvider providerIntOrDigitStringValidValues
     * @param int|string $value
     * @param int|string $expected
     * @return void
     */
    public function testEnsureIntOrDigitStringReturnsExpectedValue($value, $expected)
    {
        $actual = self::ensureIntOrDigitString($value);

        $this->assertSame($expected, $actual);
    }

    /**
     * @return array<string, array{int|string, int|string}>
     */
    public static function providerIntOrDigitStringValidValues()
    {
        return [
            'case 1' => [80, 80],
            'case 2' => ['080', '080'],
            'case 3' => ['0', '0'],
        ];
    }

    /**
     * @dataProvider providerIntOrDigitStringInvalidValues
     * @param bool|float|string $value
     * @return void
     */
    public function testEnsureIntOrDigitStringRejectsInvalidValues($value)
    {
        $this->expectException(UnexpectedValueException::class);

        self::ensureIntOrDigitString($value);
    }

    /**
     * @return array<string, array{bool|float|string}>
     */
    public static function providerIntOrDigitStringInvalidValues()
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
     * @dataProvider providerIntOrDigitStringRangeValidValues
     * @param int|string $value
     * @param int        $minimum
     * @param int        $maximum
     * @param int|string $expected
     * @return void
     */
    public function testEnsureIntOrDigitStringRangeReturnsExpectedValue($value, $minimum, $maximum, $expected)
    {
        $actual = self::ensureIntOrDigitStringRange($value, $minimum, $maximum);

        $this->assertSame($expected, $actual);
    }

    /**
     * @return array<string, array{int|string, int, int, int|string}>
     */
    public static function providerIntOrDigitStringRangeValidValues()
    {
        return [
            'case 1' => [1, 1, 65535, 1],
            'case 2' => ['080', 1, 65535, '080'],
            'case 3' => ['65535', 1, 65535, '65535'],
        ];
    }

    /**
     * @dataProvider providerIntOrDigitStringRangeInvalidValues
     * @param bool|int|string $value
     * @param int             $minimum
     * @param int             $maximum
     * @return void
     */
    public function testEnsureIntOrDigitStringRangeRejectsInvalidValues($value, $minimum, $maximum)
    {
        $this->expectException(UnexpectedValueException::class);

        self::ensureIntOrDigitStringRange($value, $minimum, $maximum);
    }

    /**
     * @return array<string, array{bool|int|string, int, int}>
     */
    public static function providerIntOrDigitStringRangeInvalidValues()
    {
        return [
            'case 1' => [0, 1, 65535],
            'case 2' => [65536, 1, 65535],
            'case 3' => ['99999999999999999999999999', 1, 65535],
            'case 4' => [false, 1, 65535],
        ];
    }

    /**
     * @dataProvider providerDigitStringValidValues
     * @param string $value
     * @param string $expected
     * @return void
     */
    public function testEnsureDigitStringReturnsExpectedValue($value, $expected)
    {
        $actual = self::ensureDigitString($value);

        $this->assertSame($expected, $actual);
    }

    /**
     * @return array<string, array{string, string}>
     */
    public static function providerDigitStringValidValues()
    {
        return [
            'case 1' => ['0', '0'],
            'case 2' => ['080', '080'],
            'case 3' => ['123', '123'],
        ];
    }

    /**
     * @dataProvider providerDigitStringInvalidValues
     * @param bool|int|string $value
     * @return void
     */
    public function testEnsureDigitStringRejectsInvalidValues($value)
    {
        $this->expectException(UnexpectedValueException::class);

        self::ensureDigitString($value);
    }

    /**
     * @return array<string, array{bool|int|string}>
     */
    public static function providerDigitStringInvalidValues()
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
     * @dataProvider providerDigitStringRangeValidValues
     * @param string $value
     * @param int    $minimum
     * @param int    $maximum
     * @param string $expected
     * @return void
     */
    public function testEnsureDigitStringRangeReturnsExpectedValue($value, $minimum, $maximum, $expected)
    {
        $actual = self::ensureDigitStringRange($value, $minimum, $maximum);

        $this->assertSame($expected, $actual);
    }

    /**
     * @return array<string, array{string, int, int, string}>
     */
    public static function providerDigitStringRangeValidValues()
    {
        return [
            'case 1' => ['1', 1, 10, '1'],
            'case 2' => ['10', 1, 10, '10'],
        ];
    }

    /**
     * @dataProvider providerDigitStringRangeInvalidValues
     * @param string $value
     * @param int    $minimum
     * @param int    $maximum
     * @return void
     */
    public function testEnsureDigitStringRangeRejectsInvalidValues($value, $minimum, $maximum)
    {
        $this->expectException(UnexpectedValueException::class);

        self::ensureDigitStringRange($value, $minimum, $maximum);
    }

    /**
     * @return array<string, array{string, int, int}>
     */
    public static function providerDigitStringRangeInvalidValues()
    {
        return [
            'case 1' => ['0', 1, 10],
            'case 2' => ['11', 1, 10],
            'case 3' => ['1e1', 1, 10],
        ];
    }

    /**
     * @dataProvider providerNonNegativeNumberValidValues
     * @param float|int $value
     * @param float|int $expected
     * @return void
     */
    public function testEnsureNonNegativeNumberReturnsExpectedValue($value, $expected)
    {
        $actual = self::ensureNonNegativeNumber($value);

        $this->assertSame($expected, $actual);
    }

    /**
     * @return array<string, array{float|int, float|int}>
     */
    public static function providerNonNegativeNumberValidValues()
    {
        return [
            'case 1' => [0, 0],
            'case 2' => [1, 1],
            'case 3' => [0.5, 0.5],
        ];
    }

    /**
     * @dataProvider providerNonNegativeNumberInvalidValues
     * @param bool|float|int|string $value
     * @return void
     */
    public function testEnsureNonNegativeNumberRejectsInvalidValues($value)
    {
        $this->expectException(UnexpectedValueException::class);

        self::ensureNonNegativeNumber($value);
    }

    /**
     * @return array<string, array{bool|float|int|string}>
     */
    public static function providerNonNegativeNumberInvalidValues()
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
     * @dataProvider providerNullableNonNegativeNumberValidValues
     * @param float|int|null $value
     * @param float|int|null $expected
     * @return void
     */
    public function testEnsureNullableNonNegativeNumberReturnsExpectedValue($value, $expected)
    {
        $actual = self::ensureNullableNonNegativeNumber($value);

        $this->assertSame($expected, $actual);
    }

    /**
     * @return array<string, array{float|int|null, float|int|null}>
     */
    public static function providerNullableNonNegativeNumberValidValues()
    {
        return [
            'case 1' => [null, null],
            'case 2' => [0, 0],
            'case 3' => [0.5, 0.5],
        ];
    }

    /**
     * @dataProvider providerNullableNonNegativeNumberInvalidValues
     * @param float|int|string $value
     * @return void
     */
    public function testEnsureNullableNonNegativeNumberRejectsInvalidValues($value)
    {
        $this->expectException(UnexpectedValueException::class);

        self::ensureNullableNonNegativeNumber($value);
    }

    /**
     * @return array<string, array{float|int|string}>
     */
    public static function providerNullableNonNegativeNumberInvalidValues()
    {
        return [
            'case 1' => [-1],
            'case 2' => [INF],
            'case 3' => [NAN],
            'case 4' => ['1'],
        ];
    }

    /**
     * @dataProvider providerArrayValidValues
     * @param array{'key': int}|array{} $value
     * @param array{'key': int}|array{} $expected
     * @return void
     */
    public function testEnsureArrayReturnsExpectedValue($value, $expected)
    {
        $actual = self::ensureArray($value);

        $this->assertSame($expected, $actual);
    }

    /**
     * @return array<string, array{array{'key': int}|array{}, array{'key': int}|array{}}>
     */
    public static function providerArrayValidValues()
    {
        return [
            'case 1' => [[], []],
            'case 2' => [['key' => 1], ['key' => 1]],
        ];
    }

    /**
     * @dataProvider providerArrayInvalidValues
     * @param null|stdClass|string $value
     * @return void
     */
    public function testEnsureArrayRejectsInvalidValues($value)
    {
        $this->expectException(UnexpectedValueException::class);

        self::ensureArray($value);
    }

    /**
     * @return array<string, array{null|stdClass|string}>
     */
    public static function providerArrayInvalidValues()
    {
        return [
            'case 1' => [null],
            'case 2' => ['array'],
            'case 3' => [new stdClass()],
        ];
    }

    /**
     * @dataProvider providerNullableArrayValidValues
     * @param array{'key': int}|array{}|null $value
     * @param array{'key': int}|array{}|null $expected
     * @return void
     */
    public function testEnsureNullableArrayReturnsExpectedValue($value, $expected)
    {
        $actual = self::ensureNullableArray($value);

        $this->assertSame($expected, $actual);
    }

    /**
     * @return array<string, array{array{'key': int}|array{}|null, array{'key': int}|array{}|null}>
     */
    public static function providerNullableArrayValidValues()
    {
        return [
            'case 1' => [null, null],
            'case 2' => [[], []],
            'case 3' => [['key' => 1], ['key' => 1]],
        ];
    }

    /**
     * @dataProvider providerNullableArrayInvalidValues
     * @param bool|stdClass|string $value
     * @return void
     */
    public function testEnsureNullableArrayRejectsInvalidValues($value)
    {
        $this->expectException(UnexpectedValueException::class);

        self::ensureNullableArray($value);
    }

    /**
     * @return array<string, array{bool|stdClass|string}>
     */
    public static function providerNullableArrayInvalidValues()
    {
        return [
            'case 1' => [false],
            'case 2' => ['array'],
            'case 3' => [new stdClass()],
        ];
    }

    /**
     * @dataProvider providerNonEmptyArrayValidValues
     * @param array{'key': null}|array{int} $value
     * @param array{'key': null}|array{int} $expected
     * @return void
     */
    public function testEnsureNonEmptyArrayReturnsExpectedValue($value, $expected)
    {
        $actual = self::ensureNonEmptyArray($value);

        $this->assertSame($expected, $actual);
    }

    /**
     * @return array<string, array{array{'key': null}|array{int}, array{'key': null}|array{int}}>
     */
    public static function providerNonEmptyArrayValidValues()
    {
        return [
            'case 1' => [['key' => null], ['key' => null]],
            'case 2' => [[1], [1]],
        ];
    }

    /**
     * @dataProvider providerNonEmptyArrayInvalidValues
     * @param array{}|null|string $value
     * @return void
     */
    public function testEnsureNonEmptyArrayRejectsInvalidValues($value)
    {
        $this->expectException(UnexpectedValueException::class);

        self::ensureNonEmptyArray($value);
    }

    /**
     * @return array<string, array{array{}|null|string}>
     */
    public static function providerNonEmptyArrayInvalidValues()
    {
        return [
            'case 1' => [[]],
            'case 2' => [null],
            'case 3' => ['array'],
        ];
    }

    /**
     * @dataProvider providerArrayHasKeyValidValues
     * @param array{'key': null}|array{bool} $value
     * @param int|string                     $key
     * @param array{'key': null}|array{bool} $expected
     * @return void
     */
    public function testEnsureArrayHasKeyReturnsExpectedValue($value, $key, $expected)
    {
        $actual = self::ensureArrayHasKey($value, $key);

        $this->assertSame($expected, $actual);
    }

    /**
     * @return array<string, array{array{'key': null}|array{bool}, int|string, array{'key': null}|array{bool}}>
     */
    public static function providerArrayHasKeyValidValues()
    {
        return [
            'case 1' => [['key' => null], 'key', ['key' => null]],
            'case 2' => [[0 => false], 0, [0 => false]],
        ];
    }

    /**
     * @dataProvider providerArrayHasKeyInvalidValues
     * @param array{'other': int}|array{} $value
     * @param string                      $key
     * @return void
     */
    public function testEnsureArrayHasKeyRejectsInvalidValues($value, $key)
    {
        $this->expectException(UnexpectedValueException::class);

        self::ensureArrayHasKey($value, $key);
    }

    /**
     * @return array<string, array{array{'other': int}|array{}, string}>
     */
    public static function providerArrayHasKeyInvalidValues()
    {
        return [
            'case 1' => [[], 'key'],
            'case 2' => [['other' => 1], 'key'],
        ];
    }

    /**
     * @dataProvider providerStringArrayValidValues
     * @param array{'key': string}|array{2: string, 8: string}|array{} $value
     * @param array{'key': string}|array{2: string, 8: string}|array{} $expected
     * @return void
     */
    public function testEnsureStringArrayReturnsExpectedValue($value, $expected)
    {
        $actual = self::ensureStringArray($value);

        $this->assertSame($expected, $actual);
    }

    /**
     * @return array<string, array{array{'key': string}|array{2: string, 8: string}|array{}, array{'key': string}|array{2: string, 8: string}|array{}}>
     */
    public static function providerStringArrayValidValues()
    {
        return [
            'case 1' => [[], []],
            'case 2' => [[2 => 'a', 8 => 'b'], [2 => 'a', 8 => 'b']],
            'case 3' => [['key' => 'value'], ['key' => 'value']],
        ];
    }

    /**
     * @dataProvider providerStringArrayInvalidValues
     * @param array{int}|array{string, bool}|bool $value
     * @return void
     */
    public function testEnsureStringArrayRejectsInvalidValues($value)
    {
        $this->expectException(UnexpectedValueException::class);

        self::ensureStringArray($value);
    }

    /**
     * @return array<string, array{array{int}|array{string, bool}|bool}>
     */
    public static function providerStringArrayInvalidValues()
    {
        return [
            'case 1' => [['a', false]],
            'case 2' => [false],
            'case 3' => [[1]],
        ];
    }

    /**
     * @dataProvider providerNullableStringArrayValidValues
     * @param array{2: string}|array{}|null $value
     * @param array{2: string}|array{}|null $expected
     * @return void
     */
    public function testEnsureNullableStringArrayReturnsExpectedValue($value, $expected)
    {
        $actual = self::ensureNullableStringArray($value);

        $this->assertSame($expected, $actual);
    }

    /**
     * @return array<string, array{array{2: string}|array{}|null, array{2: string}|array{}|null}>
     */
    public static function providerNullableStringArrayValidValues()
    {
        return [
            'case 1' => [null, null],
            'case 2' => [[], []],
            'case 3' => [[2 => 'a'], [2 => 'a']],
        ];
    }

    /**
     * @dataProvider providerNullableStringArrayInvalidValues
     * @param array{string, bool}|bool $value
     * @return void
     */
    public function testEnsureNullableStringArrayRejectsInvalidValues($value)
    {
        $this->expectException(UnexpectedValueException::class);

        self::ensureNullableStringArray($value);
    }

    /**
     * @return array<string, array{array{string, bool}|bool}>
     */
    public static function providerNullableStringArrayInvalidValues()
    {
        return [
            'case 1' => [['a', false]],
            'case 2' => [false],
        ];
    }

    /**
     * @dataProvider providerNullableStringOrStringArrayValidValues
     * @param array{2: string, 8: string}|null|string $value
     * @param array{2: string, 8: string}|null|string $expected
     * @return void
     */
    public function testEnsureNullableStringOrStringArrayReturnsExpectedValue($value, $expected)
    {
        $actual = self::ensureNullableStringOrStringArray($value);

        $this->assertSame($expected, $actual);
    }

    /**
     * @return array<string, array{array{2: string, 8: string}|null|string, array{2: string, 8: string}|null|string}>
     */
    public static function providerNullableStringOrStringArrayValidValues()
    {
        return [
            'case 1' => [null, null],
            'case 2' => ['echo', 'echo'],
            'case 3' => [[2 => 'a', 8 => 'b'], [2 => 'a', 8 => 'b']],
        ];
    }

    /**
     * @dataProvider providerNullableStringOrStringArrayInvalidValues
     * @param array{string, bool}|bool|int $value
     * @return void
     */
    public function testEnsureNullableStringOrStringArrayRejectsInvalidValues($value)
    {
        $this->expectException(UnexpectedValueException::class);

        self::ensureNullableStringOrStringArray($value);
    }

    /**
     * @return array<string, array{array{string, bool}|bool|int}>
     */
    public static function providerNullableStringOrStringArrayInvalidValues()
    {
        return [
            'case 1' => [['a', false]],
            'case 2' => [false],
            'case 3' => [1],
        ];
    }

    /**
     * @dataProvider providerStringOrArrayValidValues
     * @param array{'key': null}|array{}|string $value
     * @param array{'key': null}|array{}|string $expected
     * @return void
     */
    public function testEnsureStringOrArrayReturnsExpectedValue($value, $expected)
    {
        $actual = self::ensureStringOrArray($value);

        $this->assertSame($expected, $actual);
    }

    /**
     * @return array<string, array{array{'key': null}|array{}|string, array{'key': null}|array{}|string}>
     */
    public static function providerStringOrArrayValidValues()
    {
        return [
            'case 1' => ['', ''],
            'case 2' => [[], []],
            'case 3' => [['key' => null], ['key' => null]],
        ];
    }

    /**
     * @dataProvider providerStringOrArrayInvalidValues
     * @param bool|null|stdClass $value
     * @return void
     */
    public function testEnsureStringOrArrayRejectsInvalidValues($value)
    {
        $this->expectException(UnexpectedValueException::class);

        self::ensureStringOrArray($value);
    }

    /**
     * @return array<string, array{bool|null|stdClass}>
     */
    public static function providerStringOrArrayInvalidValues()
    {
        return [
            'case 1' => [null],
            'case 2' => [false],
            'case 3' => [new stdClass()],
        ];
    }

    /**
     * @dataProvider providerStringKeyedArrayValidValues
     * @param array{'key': null}|array{'key': stdClass}|array{} $value
     * @param array{'key': null}|array{'key': stdClass}|array{} $expected
     * @return void
     */
    public function testEnsureStringKeyedArrayReturnsExpectedValue($value, $expected)
    {
        $actual = self::ensureStringKeyedArray($value);

        $this->assertSame($expected, $actual);
    }

    /**
     * @return array<string, array{array{'key': null}|array{'key': stdClass}|array{}, array{'key': null}|array{'key': stdClass}|array{}}>
     */
    public static function providerStringKeyedArrayValidValues()
    {
        $value2 = ['key' => new stdClass()];

        return [
            'case 1' => [[], []],
            'case 2' => [['key' => null], ['key' => null]],
            'case 3' => [$value2, $value2],
        ];
    }

    /**
     * @dataProvider providerStringKeyedArrayInvalidValues
     * @param array{int}|bool $value
     * @return void
     */
    public function testEnsureStringKeyedArrayRejectsInvalidValues($value)
    {
        $this->expectException(UnexpectedValueException::class);

        self::ensureStringKeyedArray($value);
    }

    /**
     * @return array<string, array{array{int}|bool}>
     */
    public static function providerStringKeyedArrayInvalidValues()
    {
        return [
            'case 1' => [[0 => 1]],
            'case 2' => [false],
        ];
    }

    /**
     * @dataProvider providerStringMapValidValues
     * @param array{'key': string}|array{} $value
     * @param array{'key': string}|array{} $expected
     * @return void
     */
    public function testEnsureStringMapReturnsExpectedValue($value, $expected)
    {
        $actual = self::ensureStringMap($value);

        $this->assertSame($expected, $actual);
    }

    /**
     * @return array<string, array{array{'key': string}|array{}, array{'key': string}|array{}}>
     */
    public static function providerStringMapValidValues()
    {
        return [
            'case 1' => [[], []],
            'case 2' => [['key' => ''], ['key' => '']],
            'case 3' => [['key' => 'value'], ['key' => 'value']],
        ];
    }

    /**
     * @dataProvider providerStringMapInvalidValues
     * @param array{'key': bool}|array{string}|bool $value
     * @return void
     */
    public function testEnsureStringMapRejectsInvalidValues($value)
    {
        $this->expectException(UnexpectedValueException::class);

        self::ensureStringMap($value);
    }

    /**
     * @return array<string, array{array{'key': bool}|array{string}|bool}>
     */
    public static function providerStringMapInvalidValues()
    {
        return [
            'case 1' => [['key' => false]],
            'case 2' => [[0 => 'value']],
            'case 3' => [false],
        ];
    }

    /**
     * @dataProvider providerNullableStringMapValidValues
     * @param array{'key': string}|array{}|null $value
     * @param array{'key': string}|array{}|null $expected
     * @return void
     */
    public function testEnsureNullableStringMapReturnsExpectedValue($value, $expected)
    {
        $actual = self::ensureNullableStringMap($value);

        $this->assertSame($expected, $actual);
    }

    /**
     * @return array<string, array{array{'key': string}|array{}|null, array{'key': string}|array{}|null}>
     */
    public static function providerNullableStringMapValidValues()
    {
        return [
            'case 1' => [null, null],
            'case 2' => [[], []],
            'case 3' => [['key' => 'value'], ['key' => 'value']],
        ];
    }

    /**
     * @dataProvider providerNullableStringMapInvalidValues
     * @param array{'key': bool}|bool $value
     * @return void
     */
    public function testEnsureNullableStringMapRejectsInvalidValues($value)
    {
        $this->expectException(UnexpectedValueException::class);

        self::ensureNullableStringMap($value);
    }

    /**
     * @return array<string, array{array{'key': bool}|bool}>
     */
    public static function providerNullableStringMapInvalidValues()
    {
        return [
            'case 1' => [['key' => false]],
            'case 2' => [false],
        ];
    }

    /**
     * @dataProvider providerNullableBoolOrStringValidValues
     * @param bool|null|string $value
     * @param bool|null|string $expected
     * @return void
     */
    public function testEnsureNullableBoolOrStringReturnsExpectedValue($value, $expected)
    {
        $actual = self::ensureNullableBoolOrString($value);

        $this->assertSame($expected, $actual);
    }

    /**
     * @return array<string, array{bool|null|string, bool|null|string}>
     */
    public static function providerNullableBoolOrStringValidValues()
    {
        return [
            'case 1' => [null, null],
            'case 2' => [true, true],
            'case 3' => [false, false],
            'case 4' => ['value', 'value'],
        ];
    }

    /**
     * @dataProvider providerNullableBoolOrStringInvalidValues
     * @param array{}|int|stdClass $value
     * @return void
     */
    public function testEnsureNullableBoolOrStringRejectsInvalidValues($value)
    {
        $this->expectException(UnexpectedValueException::class);

        self::ensureNullableBoolOrString($value);
    }

    /**
     * @return array<string, array{array{}|int|stdClass}>
     */
    public static function providerNullableBoolOrStringInvalidValues()
    {
        return [
            'case 1' => [1],
            'case 2' => [[]],
            'case 3' => [new stdClass()],
        ];
    }

    /**
     * @dataProvider providerObjectValidValues
     * @param stdClass $value
     * @param stdClass $expected
     * @return void
     */
    public function testEnsureObjectReturnsExpectedValue($value, $expected)
    {
        $actual = self::ensureObject($value);

        $this->assertSame($expected, $actual);
    }

    /**
     * @return array<string, array{stdClass, stdClass}>
     */
    public static function providerObjectValidValues()
    {
        $value0 = new stdClass();

        return [
            'case 1' => [$value0, $value0],
        ];
    }

    /**
     * @dataProvider providerObjectInvalidValues
     * @param array{}|null|string $value
     * @return void
     */
    public function testEnsureObjectRejectsInvalidValues($value)
    {
        $this->expectException(UnexpectedValueException::class);

        self::ensureObject($value);
    }

    /**
     * @return array<string, array{array{}|null|string}>
     */
    public static function providerObjectInvalidValues()
    {
        return [
            'case 1' => [null],
            'case 2' => [[]],
            'case 3' => ['object'],
        ];
    }

    /**
     * @dataProvider providerClassStringValidValues
     * @param string $value
     * @param string $expected
     * @return void
     */
    public function testEnsureClassStringReturnsExpectedValue($value, $expected)
    {
        $actual = self::ensureClassString($value);

        $this->assertSame($expected, $actual);
    }

    /**
     * @return array<string, array{string, string}>
     */
    public static function providerClassStringValidValues()
    {
        return [
            'case 1' => [stdClass::class, stdClass::class],
            'case 2' => [Traversable::class, Traversable::class],
        ];
    }

    /**
     * @dataProvider providerClassStringInvalidValues
     * @param bool|stdClass|string $value
     * @return void
     */
    public function testEnsureClassStringRejectsInvalidValues($value)
    {
        $this->expectException(UnexpectedValueException::class);

        self::ensureClassString($value);
    }

    /**
     * @return array<string, array{bool|stdClass|string}>
     */
    public static function providerClassStringInvalidValues()
    {
        return [
            'case 1' => ['ClassThatDoesNotExist'],
            'case 2' => [false],
            'case 3' => [new stdClass()],
        ];
    }

    /**
     * @dataProvider providerInstanceOfValidValues
     * @param stdClass $value
     * @param string   $class
     * @param stdClass $expected
     * @return void
     */
    public function testEnsureInstanceOfReturnsExpectedValue($value, $class, $expected)
    {
        $actual = self::ensureInstanceOf($value, $class);

        $this->assertSame($expected, $actual);
    }

    /**
     * @return array<string, array{stdClass, string, stdClass}>
     */
    public static function providerInstanceOfValidValues()
    {
        $value0 = new stdClass();

        return [
            'case 1' => [$value0, stdClass::class, $value0],
        ];
    }

    /**
     * @dataProvider providerInstanceOfInvalidValues
     * @param array{}|null|stdClass $value
     * @param string                $class
     * @return void
     */
    public function testEnsureInstanceOfRejectsInvalidValues($value, $class)
    {
        $this->expectException(UnexpectedValueException::class);

        self::ensureInstanceOf($value, $class);
    }

    /**
     * @return array<string, array{array{}|null|stdClass, string}>
     */
    public static function providerInstanceOfInvalidValues()
    {
        return [
            'case 1' => [null, stdClass::class],
            'case 2' => [[], stdClass::class],
            'case 3' => [new stdClass(), Traversable::class],
        ];
    }

    /**
     * @dataProvider providerNullableInstanceOfValidValues
     * @param null|stdClass $value
     * @param string        $class
     * @param null|stdClass $expected
     * @return void
     */
    public function testEnsureNullableInstanceOfReturnsExpectedValue($value, $class, $expected)
    {
        $actual = self::ensureNullableInstanceOf($value, $class);

        $this->assertSame($expected, $actual);
    }

    /**
     * @return array<string, array{null|stdClass, string, null|stdClass}>
     */
    public static function providerNullableInstanceOfValidValues()
    {
        $value1 = new stdClass();

        return [
            'case 1' => [null, stdClass::class, null],
            'case 2' => [$value1, stdClass::class, $value1],
        ];
    }

    /**
     * @dataProvider providerNullableInstanceOfInvalidValues
     * @param array{}|bool $value
     * @param string       $class
     * @return void
     */
    public function testEnsureNullableInstanceOfRejectsInvalidValues($value, $class)
    {
        $this->expectException(UnexpectedValueException::class);

        self::ensureNullableInstanceOf($value, $class);
    }

    /**
     * @return array<string, array{array{}|bool, string}>
     */
    public static function providerNullableInstanceOfInvalidValues()
    {
        return [
            'case 1' => [[], stdClass::class],
            'case 2' => [false, stdClass::class],
        ];
    }

    /**
     * @dataProvider providerNonEmptyStringOrInstanceOfValidValues
     * @param stdClass|string $value
     * @param string          $class
     * @param stdClass|string $expected
     * @return void
     */
    public function testEnsureNonEmptyStringOrInstanceOfReturnsExpectedValue($value, $class, $expected)
    {
        $actual = self::ensureNonEmptyStringOrInstanceOf($value, $class);

        $this->assertSame($expected, $actual);
    }

    /**
     * @return array<string, array{stdClass|string, string, stdClass|string}>
     */
    public static function providerNonEmptyStringOrInstanceOfValidValues()
    {
        $value1 = new stdClass();

        return [
            'case 1' => ['value', stdClass::class, 'value'],
            'case 2' => [$value1, stdClass::class, $value1],
        ];
    }

    /**
     * @dataProvider providerNonEmptyStringOrInstanceOfInvalidValues
     * @param bool|null|string $value
     * @param string           $class
     * @return void
     */
    public function testEnsureNonEmptyStringOrInstanceOfRejectsInvalidValues($value, $class)
    {
        $this->expectException(UnexpectedValueException::class);

        self::ensureNonEmptyStringOrInstanceOf($value, $class);
    }

    /**
     * @return array<string, array{bool|null|string, string}>
     */
    public static function providerNonEmptyStringOrInstanceOfInvalidValues()
    {
        return [
            'case 1' => ['', stdClass::class],
            'case 2' => [null, stdClass::class],
            'case 3' => [false, stdClass::class],
        ];
    }

    /**
     * @dataProvider providerStringOrArrayOrInstanceOfValidValues
     * @param array{}|stdClass|string $value
     * @param string                  $class
     * @param array{}|stdClass|string $expected
     * @return void
     */
    public function testEnsureStringOrArrayOrInstanceOfReturnsExpectedValue($value, $class, $expected)
    {
        $actual = self::ensureStringOrArrayOrInstanceOf($value, $class);

        $this->assertSame($expected, $actual);
    }

    /**
     * @return array<string, array{array{}|stdClass|string, string, array{}|stdClass|string}>
     */
    public static function providerStringOrArrayOrInstanceOfValidValues()
    {
        $value2 = new stdClass();

        return [
            'case 1' => ['value', stdClass::class, 'value'],
            'case 2' => [[], stdClass::class, []],
            'case 3' => [$value2, stdClass::class, $value2],
        ];
    }

    /**
     * @dataProvider providerStringOrArrayOrInstanceOfInvalidValues
     * @param bool|null $value
     * @param string    $class
     * @return void
     */
    public function testEnsureStringOrArrayOrInstanceOfRejectsInvalidValues($value, $class)
    {
        $this->expectException(UnexpectedValueException::class);

        self::ensureStringOrArrayOrInstanceOf($value, $class);
    }

    /**
     * @return array<string, array{bool|null, string}>
     */
    public static function providerStringOrArrayOrInstanceOfInvalidValues()
    {
        return [
            'case 1' => [false, stdClass::class],
            'case 2' => [null, stdClass::class],
        ];
    }

    /**
     * @dataProvider providerNullValidValues
     * @param null $value
     * @param null $expected
     * @return void
     */
    public function testEnsureNullReturnsExpectedValue($value, $expected)
    {
        $actual = self::ensureNull($value);

        $this->assertSame($expected, $actual);
    }

    /**
     * @return array<string, array{null, null}>
     */
    public static function providerNullValidValues()
    {
        return [
            'case 1' => [null, null],
        ];
    }

    /**
     * @dataProvider providerNullInvalidValues
     * @param array{}|bool|int|string $value
     * @return void
     */
    public function testEnsureNullRejectsInvalidValues($value)
    {
        $this->expectException(UnexpectedValueException::class);

        self::ensureNull($value);
    }

    /**
     * @return array<string, array{array{}|bool|int|string}>
     */
    public static function providerNullInvalidValues()
    {
        return [
            'case 1' => [false],
            'case 2' => [0],
            'case 3' => [''],
            'case 4' => [[]],
        ];
    }

    /**
     * @dataProvider providerOneOfValidValues
     * @param string                $value
     * @param array{string, string} $values
     * @param string                $expected
     * @return void
     */
    public function testEnsureOneOfReturnsExpectedValue($value, $values, $expected)
    {
        $actual = self::ensureOneOf($value, $values);

        $this->assertSame($expected, $actual);
    }

    /**
     * @return array<string, array{string, array{string, string}, string}>
     */
    public static function providerOneOfValidValues()
    {
        return [
            'case 1' => ['one', ['one', 'two'], 'one'],
        ];
    }

    /**
     * @dataProvider providerOneOfInvalidValues
     * @param int|string                          $value
     * @param array{string, string}|array{string} $values
     * @return void
     */
    public function testEnsureOneOfRejectsInvalidValues($value, $values)
    {
        $this->expectException(InvalidFormatException::class);

        self::ensureOneOf($value, $values);
    }

    /**
     * @return array<string, array{int|string, array{string, string}|array{string}}>
     */
    public static function providerOneOfInvalidValues()
    {
        return [
            'case 1' => ['three', ['one', 'two']],
            'case 2' => [1, ['1']],
        ];
    }

    /**
     * @dataProvider providerMatchesValidValues
     * @param string $value
     * @param string $pattern
     * @param string $expected
     * @return void
     */
    public function testEnsureMatchesReturnsExpectedValue($value, $pattern, $expected)
    {
        $actual = self::ensureMatches($value, $pattern);

        $this->assertSame($expected, $actual);
    }

    /**
     * @return array<string, array{string, string, string}>
     */
    public static function providerMatchesValidValues()
    {
        return [
            'case 1' => ['123', '/^[0-9]+$/', '123'],
        ];
    }

    /**
     * @dataProvider providerMatchesInvalidValues
     * @param string $value
     * @param string $pattern
     * @return void
     */
    public function testEnsureMatchesRejectsInvalidValues($value, $pattern)
    {
        $this->expectException(InvalidFormatException::class);

        self::ensureMatches($value, $pattern);
    }

    /**
     * @return array<string, array{string, string}>
     */
    public static function providerMatchesInvalidValues()
    {
        return [
            'case 1' => ['123x', '/^[0-9]+$/'],
        ];
    }

    /**
     * @dataProvider providerNullableStreamValidValues
     * @param null|resource $value
     * @param null|resource $expected
     * @return void
     */
    public function testEnsureNullableStreamReturnsExpectedValue($value, $expected)
    {
        $actual = self::ensureNullableStream($value);

        $this->assertSame($expected, $actual);
    }

    /**
     * @return array<string, array{null|resource, null|resource}>
     */
    public static function providerNullableStreamValidValues()
    {
        $value1 = fopen('php://memory', 'w+');

        return [
            'case 1' => [null, null],
            'case 2' => [$value1, $value1],
        ];
    }

    /**
     * @dataProvider providerNullableStreamInvalidValues
     * @param bool|stdClass|string $value
     * @return void
     */
    public function testEnsureNullableStreamRejectsInvalidValues($value)
    {
        $this->expectException(UnexpectedValueException::class);

        self::ensureNullableStream($value);
    }

    /**
     * @return array<string, array{bool|stdClass|string}>
     */
    public static function providerNullableStreamInvalidValues()
    {
        return [
            'case 1' => [false],
            'case 2' => ['stream'],
            'case 3' => [new stdClass()],
        ];
    }

    /**
     * @dataProvider providerStreamValidValues
     * @param resource $value
     * @param resource $expected
     * @return void
     */
    public function testEnsureStreamReturnsExpectedValue($value, $expected)
    {
        $actual = self::ensureStream($value);

        $this->assertSame($expected, $actual);
    }

    /**
     * @return array<string, array{resource, resource}>
     */
    public static function providerStreamValidValues()
    {
        $value0 = fopen('php://memory', 'w+');

        return [
            'case 1' => [$value0, $value0],
        ];
    }

    /**
     * @dataProvider providerStreamInvalidValues
     * @param bool|null|stdClass|string $value
     * @return void
     */
    public function testEnsureStreamRejectsInvalidValues($value)
    {
        $this->expectException(UnexpectedValueException::class);

        self::ensureStream($value);
    }

    /**
     * @return array<string, array{bool|null|stdClass|string}>
     */
    public static function providerStreamInvalidValues()
    {
        return [
            'case 1' => [null],
            'case 2' => ['php://memory'],
            'case 3' => [false],
            'case 4' => [new stdClass()],
        ];
    }

    /**
     * @dataProvider providerNullableScalarOrStreamOrTraversableValidValues
     * @param ArrayIterator<never, never>|bool|int|null|resource|string $value
     * @param ArrayIterator<never, never>|bool|int|null|resource|string $expected
     * @return void
     */
    public function testEnsureNullableScalarOrStreamOrTraversableReturnsExpectedValue($value, $expected)
    {
        $actual = self::ensureNullableScalarOrStreamOrTraversable($value);

        $this->assertSame($expected, $actual);
    }

    /**
     * @return array<string, array{ArrayIterator<never, never>|bool|int|null|resource|string, ArrayIterator<never, never>|bool|int|null|resource|string}>
     */
    public static function providerNullableScalarOrStreamOrTraversableValidValues()
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
     * @dataProvider providerNullableScalarOrStreamOrTraversableInvalidValues
     * @param array{}|stdClass $value
     * @return void
     */
    public function testEnsureNullableScalarOrStreamOrTraversableRejectsInvalidValues($value)
    {
        $this->expectException(UnexpectedValueException::class);

        self::ensureNullableScalarOrStreamOrTraversable($value);
    }

    /**
     * @return array<string, array{array{}|stdClass}>
     */
    public static function providerNullableScalarOrStreamOrTraversableInvalidValues()
    {
        return [
            'case 1' => [[]],
            'case 2' => [new stdClass()],
        ];
    }

    /**
     * @dataProvider providerClassStringOfValidValues
     * @param string $value
     * @param string $class
     * @param string $expected
     * @return void
     */
    public function testEnsureClassStringOfReturnsExpectedValue($value, $class, $expected)
    {
        $actual = self::ensureClassStringOf($value, $class);

        $this->assertSame($expected, $actual);
    }

    /**
     * @return array<string, array{string, string, string}>
     */
    public static function providerClassStringOfValidValues()
    {
        return [
            'case 1' => [ArrayIterator::class, Traversable::class, ArrayIterator::class],
        ];
    }

    /**
     * @dataProvider providerClassStringOfInvalidValues
     * @param bool|string $value
     * @param string      $class
     * @return void
     */
    public function testEnsureClassStringOfRejectsInvalidValues($value, $class)
    {
        $this->expectException(UnexpectedValueException::class);

        self::ensureClassStringOf($value, $class);
    }

    /**
     * @return array<string, array{bool|string, string}>
     */
    public static function providerClassStringOfInvalidValues()
    {
        return [
            'case 1' => [stdClass::class, Traversable::class],
            'case 2' => ['MissingClass', Traversable::class],
            'case 3' => [false, Traversable::class],
        ];
    }

    /**
     * @dataProvider providerClassStringOrInstanceOfValidValues
     * @param ArrayIterator<never, never>|string $value
     * @param string                             $class
     * @param ArrayIterator<never, never>|string $expected
     * @return void
     */
    public function testEnsureClassStringOrInstanceOfReturnsExpectedValue($value, $class, $expected)
    {
        $actual = self::ensureClassStringOrInstanceOf($value, $class);

        $this->assertSame($expected, $actual);
    }

    /**
     * @return array<string, array{ArrayIterator<never, never>|string, string, ArrayIterator<never, never>|string}>
     */
    public static function providerClassStringOrInstanceOfValidValues()
    {
        $value1 = new ArrayIterator([]);

        return [
            'case 1' => [ArrayIterator::class, Traversable::class, ArrayIterator::class],
            'case 2' => [$value1, Traversable::class, $value1],
        ];
    }

    /**
     * @dataProvider providerClassStringOrInstanceOfInvalidValues
     * @param bool|stdClass|string $value
     * @param string               $class
     * @return void
     */
    public function testEnsureClassStringOrInstanceOfRejectsInvalidValues($value, $class)
    {
        $this->expectException(UnexpectedValueException::class);

        self::ensureClassStringOrInstanceOf($value, $class);
    }

    /**
     * @return array<string, array{bool|stdClass|string, string}>
     */
    public static function providerClassStringOrInstanceOfInvalidValues()
    {
        return [
            'case 1' => [stdClass::class, Traversable::class],
            'case 2' => [new stdClass(), Traversable::class],
            'case 3' => [false, Traversable::class],
        ];
    }

    /**
     * @dataProvider providerScalarOrStringableValidValues
     * @param SimpleXMLElement|bool|int|string $value
     * @param SimpleXMLElement|bool|int|string $expected
     * @return void
     */
    public function testEnsureScalarOrStringableReturnsExpectedValue($value, $expected)
    {
        $actual = self::ensureScalarOrStringable($value);

        $this->assertSame($expected, $actual);
    }

    /**
     * @return array<string, array{SimpleXMLElement|bool|int|string, SimpleXMLElement|bool|int|string}>
     */
    public static function providerScalarOrStringableValidValues()
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
     * @dataProvider providerScalarOrStringableInvalidValues
     * @param array{}|null|stdClass $value
     * @return void
     */
    public function testEnsureScalarOrStringableRejectsInvalidValues($value)
    {
        $this->expectException(UnexpectedValueException::class);

        self::ensureScalarOrStringable($value);
    }

    /**
     * @return array<string, array{array{}|null|stdClass}>
     */
    public static function providerScalarOrStringableInvalidValues()
    {
        return [
            'case 1' => [null],
            'case 2' => [[]],
            'case 3' => [new stdClass()],
        ];
    }

    /**
     * @dataProvider providerNullableScalarOrStringableOrArrayValidValues
     * @param array{SimpleXMLElement}|array{string, int}|bool|null $value
     * @param array{SimpleXMLElement}|array{string, int}|bool|null $expected
     * @return void
     */
    public function testEnsureNullableScalarOrStringableOrArrayReturnsExpectedValue($value, $expected)
    {
        $actual = self::ensureNullableScalarOrStringableOrArray($value);

        $this->assertSame($expected, $actual);
    }

    /**
     * @return array<string, array{array{SimpleXMLElement}|array{string, int}|bool|null, array{SimpleXMLElement}|array{string, int}|bool|null}>
     */
    public static function providerNullableScalarOrStringableOrArrayValidValues()
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
     * @dataProvider providerNullableScalarOrStringableOrArrayInvalidValues
     * @param array{'bad': stdClass}|array{array{int}}|stdClass $value
     * @return void
     */
    public function testEnsureNullableScalarOrStringableOrArrayRejectsInvalidValues($value)
    {
        $this->expectException(UnexpectedValueException::class);

        self::ensureNullableScalarOrStringableOrArray($value);
    }

    /**
     * @return array<string, array{array{'bad': stdClass}|array{array{int}}|stdClass}>
     */
    public static function providerNullableScalarOrStringableOrArrayInvalidValues()
    {
        return [
            'case 1' => [new stdClass()],
            'case 2' => [[[1]]],
            'case 3' => [['bad' => new stdClass()]],
        ];
    }

    /**
     * @return void
     */
    public function testEnsureStringUsesProvidedMessage()
    {
        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('A custom validation message');

        self::ensureString(false, 'A custom validation message');
    }

    /**
     * @dataProvider providerStringMapInvalidValues
     * @param array{'key': bool}|array{string}|bool $value
     * @return void
     */
    public function testEnsureStringMapUsesProvidedMessage($value)
    {
        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('A custom validation message');

        self::ensureStringMap($value, 'A custom validation message');
    }
}
