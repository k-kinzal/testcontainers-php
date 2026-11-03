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

final class EnsureTest extends TestCase
{
    use Ensure;

    /**
     * @dataProvider providerEnsureNullableScalarOrStringableOrArrayMapReturnsExpectedValue
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
    public static function providerEnsureNullableScalarOrStringableOrArrayMapReturnsExpectedValue()
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
     * @dataProvider providerEnsureNullableScalarOrStringableOrArrayMapRejectsInvalidValues
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
    public static function providerEnsureNullableScalarOrStringableOrArrayMapRejectsInvalidValues()
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
     * @dataProvider providerEnsureStringReturnsExpectedValue
     * @param string $value
     * @param string $expected
     * @return void
     * @psalm-param mixed $value
     */
    public function testEnsureStringReturnsExpectedValue($value, $expected)
    {
        $actual = self::ensureString($value);

        $this->assertSame($expected, $actual);
    }

    /**
     * @return array<string, array{string, string}>
     */
    public static function providerEnsureStringReturnsExpectedValue()
    {
        return [
            'case 1' => ['', ''],
            'case 2' => ['text', 'text'],
            'case 3' => ['0', '0'],
        ];
    }

    /**
     * @dataProvider providerEnsureStringRejectsInvalidValues
     * @param array{}|bool|int|null|stdClass $value
     * @return void
     * @psalm-param mixed $value
     */
    public function testEnsureStringRejectsInvalidValues($value)
    {
        $this->expectException(UnexpectedValueException::class);

        self::ensureString($value);
    }

    /**
     * @return array<string, array{array{}|bool|int|null|stdClass}>
     */
    public static function providerEnsureStringRejectsInvalidValues()
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
     * @dataProvider providerEnsureNonEmptyStringReturnsExpectedValue
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
    public static function providerEnsureNonEmptyStringReturnsExpectedValue()
    {
        return [
            'case 1' => ['text', 'text'],
            'case 2' => ['0', '0'],
        ];
    }

    /**
     * @dataProvider providerEnsureNonEmptyStringRejectsInvalidValues
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
    public static function providerEnsureNonEmptyStringRejectsInvalidValues()
    {
        return [
            'case 1' => [''],
            'case 2' => [null],
            'case 3' => [false],
            'case 4' => [1],
        ];
    }

    /**
     * @dataProvider providerEnsureNullableStringReturnsExpectedValue
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
    public static function providerEnsureNullableStringReturnsExpectedValue()
    {
        return [
            'case 1' => [null, null],
            'case 2' => ['', ''],
            'case 3' => ['text', 'text'],
        ];
    }

    /**
     * @dataProvider providerEnsureNullableStringRejectsInvalidValues
     * @param array{}|bool|int $value
     * @return void
     * @psalm-param mixed $value
     */
    public function testEnsureNullableStringRejectsInvalidValues($value)
    {
        $this->expectException(UnexpectedValueException::class);

        self::ensureNullableString($value);
    }

    /**
     * @return array<string, array{array{}|bool|int}>
     */
    public static function providerEnsureNullableStringRejectsInvalidValues()
    {
        return [
            'case 1' => [false],
            'case 2' => [1],
            'case 3' => [[]],
        ];
    }

    /**
     * @dataProvider providerEnsureNullableNonEmptyStringReturnsExpectedValue
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
    public static function providerEnsureNullableNonEmptyStringReturnsExpectedValue()
    {
        return [
            'case 1' => [null, null],
            'case 2' => ['text', 'text'],
            'case 3' => ['0', '0'],
        ];
    }

    /**
     * @dataProvider providerEnsureNullableNonEmptyStringRejectsInvalidValues
     * @param bool|int|string $value
     * @return void
     * @psalm-param mixed $value
     */
    public function testEnsureNullableNonEmptyStringRejectsInvalidValues($value)
    {
        $this->expectException(UnexpectedValueException::class);

        self::ensureNullableNonEmptyString($value);
    }

    /**
     * @return array<string, array{bool|int|string}>
     */
    public static function providerEnsureNullableNonEmptyStringRejectsInvalidValues()
    {
        return [
            'case 1' => [''],
            'case 2' => [false],
            'case 3' => [1],
        ];
    }

    /**
     * @dataProvider providerEnsureBoolReturnsExpectedValue
     * @param bool $value
     * @param bool $expected
     * @return void
     * @psalm-param mixed $value
     */
    public function testEnsureBoolReturnsExpectedValue($value, $expected)
    {
        $actual = self::ensureBool($value);

        $this->assertSame($expected, $actual);
    }

    /**
     * @return array<string, array{bool, bool}>
     */
    public static function providerEnsureBoolReturnsExpectedValue()
    {
        return [
            'case 1' => [true, true],
            'case 2' => [false, false],
        ];
    }

    /**
     * @dataProvider providerEnsureBoolRejectsInvalidValues
     * @param array{}|int|null|string $value
     * @return void
     * @psalm-param mixed $value
     */
    public function testEnsureBoolRejectsInvalidValues($value)
    {
        $this->expectException(UnexpectedValueException::class);

        self::ensureBool($value);
    }

    /**
     * @return array<string, array{array{}|int|null|string}>
     */
    public static function providerEnsureBoolRejectsInvalidValues()
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
     * @dataProvider providerEnsureNullableBoolReturnsExpectedValue
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
    public static function providerEnsureNullableBoolReturnsExpectedValue()
    {
        return [
            'case 1' => [null, null],
            'case 2' => [true, true],
            'case 3' => [false, false],
        ];
    }

    /**
     * @dataProvider providerEnsureNullableBoolRejectsInvalidValues
     * @param array{}|int|string $value
     * @return void
     * @psalm-param mixed $value
     */
    public function testEnsureNullableBoolRejectsInvalidValues($value)
    {
        $this->expectException(UnexpectedValueException::class);

        self::ensureNullableBool($value);
    }

    /**
     * @return array<string, array{array{}|int|string}>
     */
    public static function providerEnsureNullableBoolRejectsInvalidValues()
    {
        return [
            'case 1' => [0],
            'case 2' => ['false'],
            'case 3' => [[]],
        ];
    }

    /**
     * @dataProvider providerEnsureIntReturnsExpectedValue
     * @param int $value
     * @param int $expected
     * @return void
     * @psalm-param mixed $value
     */
    public function testEnsureIntReturnsExpectedValue($value, $expected)
    {
        $actual = self::ensureInt($value);

        $this->assertSame($expected, $actual);
    }

    /**
     * @return array<string, array{int, int}>
     */
    public static function providerEnsureIntReturnsExpectedValue()
    {
        return [
            'case 1' => [0, 0],
            'case 2' => [1, 1],
            'case 3' => [-1, -1],
        ];
    }

    /**
     * @dataProvider providerEnsureIntRejectsInvalidValues
     * @param bool|float|null|string $value
     * @return void
     * @psalm-param mixed $value
     */
    public function testEnsureIntRejectsInvalidValues($value)
    {
        $this->expectException(UnexpectedValueException::class);

        self::ensureInt($value);
    }

    /**
     * @return array<string, array{bool|float|null|string}>
     */
    public static function providerEnsureIntRejectsInvalidValues()
    {
        return [
            'case 1' => ['1'],
            'case 2' => [1.5],
            'case 3' => [true],
            'case 4' => [null],
        ];
    }

    /**
     * @dataProvider providerEnsureIntRangeReturnsExpectedValue
     * @param int      $value
     * @param int      $minimum
     * @param int|null $maximum
     * @param int      $expected
     * @return void
     * @psalm-param mixed $value
     */
    public function testEnsureIntRangeReturnsExpectedValue($value, $minimum, $maximum, $expected)
    {
        $actual = self::ensureIntRange($value, $minimum, $maximum);

        $this->assertSame($expected, $actual);
    }

    /**
     * @return array<string, array{int, int, int|null, int}>
     */
    public static function providerEnsureIntRangeReturnsExpectedValue()
    {
        return [
            'case 1' => [0, 0, 10, 0],
            'case 2' => [10, 0, 10, 10],
            'case 3' => [999, 0, null, 999],
        ];
    }

    /**
     * @dataProvider providerEnsureIntRangeRejectsInvalidValues
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
    public static function providerEnsureIntRangeRejectsInvalidValues()
    {
        return [
            'case 1' => [-1, 0, 10],
            'case 2' => [11, 0, 10],
            'case 3' => ['1', 0, 10],
        ];
    }

    /**
     * @dataProvider providerEnsureNullableIntRangeReturnsExpectedValue
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
    public static function providerEnsureNullableIntRangeReturnsExpectedValue()
    {
        return [
            'case 1' => [null, 0, 10, null],
            'case 2' => [0, 0, 10, 0],
            'case 3' => [10, 0, 10, 10],
        ];
    }

    /**
     * @dataProvider providerEnsureNullableIntRangeRejectsInvalidValues
     * @param bool|int $value
     * @param int      $minimum
     * @param int      $maximum
     * @return void
     * @psalm-param mixed $value
     */
    public function testEnsureNullableIntRangeRejectsInvalidValues($value, $minimum, $maximum)
    {
        $this->expectException(UnexpectedValueException::class);

        self::ensureNullableIntRange($value, $minimum, $maximum);
    }

    /**
     * @return array<string, array{bool|int, int, int}>
     */
    public static function providerEnsureNullableIntRangeRejectsInvalidValues()
    {
        return [
            'case 1' => [-1, 0, 10],
            'case 2' => [11, 0, 10],
            'case 3' => [false, 0, 10],
        ];
    }

    /**
     * @dataProvider providerEnsureIntOrDigitStringReturnsExpectedValue
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
    public static function providerEnsureIntOrDigitStringReturnsExpectedValue()
    {
        return [
            'case 1' => [80, 80],
            'case 2' => ['080', '080'],
            'case 3' => ['0', '0'],
        ];
    }

    /**
     * @dataProvider providerEnsureIntOrDigitStringRejectsInvalidValues
     * @param bool|float|string $value
     * @return void
     * @psalm-param mixed $value
     */
    public function testEnsureIntOrDigitStringRejectsInvalidValues($value)
    {
        $this->expectException(UnexpectedValueException::class);

        self::ensureIntOrDigitString($value);
    }

    /**
     * @return array<string, array{bool|float|string}>
     */
    public static function providerEnsureIntOrDigitStringRejectsInvalidValues()
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
     * @dataProvider providerEnsureIntOrDigitStringRangeReturnsExpectedValue
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
    public static function providerEnsureIntOrDigitStringRangeReturnsExpectedValue()
    {
        return [
            'case 1' => [1, 1, 65535, 1],
            'case 2' => ['080', 1, 65535, '080'],
            'case 3' => ['65535', 1, 65535, '65535'],
        ];
    }

    /**
     * @dataProvider providerEnsureIntOrDigitStringRangeRejectsInvalidValues
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
    public static function providerEnsureIntOrDigitStringRangeRejectsInvalidValues()
    {
        return [
            'case 1' => [0, 1, 65535],
            'case 2' => [65536, 1, 65535],
            'case 3' => ['99999999999999999999999999', 1, 65535],
            'case 4' => [false, 1, 65535],
        ];
    }

    /**
     * @dataProvider providerEnsureDigitStringReturnsExpectedValue
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
    public static function providerEnsureDigitStringReturnsExpectedValue()
    {
        return [
            'case 1' => ['0', '0'],
            'case 2' => ['080', '080'],
            'case 3' => ['123', '123'],
        ];
    }

    /**
     * @dataProvider providerEnsureDigitStringRejectsInvalidValues
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
    public static function providerEnsureDigitStringRejectsInvalidValues()
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
     * @dataProvider providerEnsureDigitStringRangeReturnsExpectedValue
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
    public static function providerEnsureDigitStringRangeReturnsExpectedValue()
    {
        return [
            'case 1' => ['1', 1, 10, '1'],
            'case 2' => ['10', 1, 10, '10'],
        ];
    }

    /**
     * @dataProvider providerEnsureDigitStringRangeRejectsInvalidValues
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
    public static function providerEnsureDigitStringRangeRejectsInvalidValues()
    {
        return [
            'case 1' => ['0', 1, 10],
            'case 2' => ['11', 1, 10],
            'case 3' => ['1e1', 1, 10],
        ];
    }

    /**
     * @dataProvider providerEnsureNonNegativeNumberReturnsExpectedValue
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
    public static function providerEnsureNonNegativeNumberReturnsExpectedValue()
    {
        return [
            'case 1' => [0, 0],
            'case 2' => [1, 1],
            'case 3' => [0.5, 0.5],
        ];
    }

    /**
     * @dataProvider providerEnsureNonNegativeNumberRejectsInvalidValues
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
    public static function providerEnsureNonNegativeNumberRejectsInvalidValues()
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
     * @dataProvider providerEnsureNullableNonNegativeNumberReturnsExpectedValue
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
    public static function providerEnsureNullableNonNegativeNumberReturnsExpectedValue()
    {
        return [
            'case 1' => [null, null],
            'case 2' => [0, 0],
            'case 3' => [0.5, 0.5],
        ];
    }

    /**
     * @dataProvider providerEnsureNullableNonNegativeNumberRejectsInvalidValues
     * @param float|int|string $value
     * @return void
     * @psalm-param mixed $value
     */
    public function testEnsureNullableNonNegativeNumberRejectsInvalidValues($value)
    {
        $this->expectException(UnexpectedValueException::class);

        self::ensureNullableNonNegativeNumber($value);
    }

    /**
     * @return array<string, array{float|int|string}>
     */
    public static function providerEnsureNullableNonNegativeNumberRejectsInvalidValues()
    {
        return [
            'case 1' => [-1],
            'case 2' => [INF],
            'case 3' => [NAN],
            'case 4' => ['1'],
        ];
    }

    /**
     * @dataProvider providerEnsureArrayReturnsExpectedValue
     * @param array{'key': int}|array{} $value
     * @param array{'key': int}|array{} $expected
     * @return void
     * @psalm-param mixed $value
     */
    public function testEnsureArrayReturnsExpectedValue($value, $expected)
    {
        $actual = self::ensureArray($value);

        $this->assertSame($expected, $actual);
    }

    /**
     * @return array<string, array{array{'key': int}|array{}, array{'key': int}|array{}}>
     */
    public static function providerEnsureArrayReturnsExpectedValue()
    {
        return [
            'case 1' => [[], []],
            'case 2' => [['key' => 1], ['key' => 1]],
        ];
    }

    /**
     * @dataProvider providerEnsureArrayRejectsInvalidValues
     * @param null|stdClass|string $value
     * @return void
     * @psalm-param mixed $value
     */
    public function testEnsureArrayRejectsInvalidValues($value)
    {
        $this->expectException(UnexpectedValueException::class);

        self::ensureArray($value);
    }

    /**
     * @return array<string, array{null|stdClass|string}>
     */
    public static function providerEnsureArrayRejectsInvalidValues()
    {
        return [
            'case 1' => [null],
            'case 2' => ['array'],
            'case 3' => [new stdClass()],
        ];
    }

    /**
     * @dataProvider providerEnsureNullableArrayReturnsExpectedValue
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
    public static function providerEnsureNullableArrayReturnsExpectedValue()
    {
        return [
            'case 1' => [null, null],
            'case 2' => [[], []],
            'case 3' => [['key' => 1], ['key' => 1]],
        ];
    }

    /**
     * @dataProvider providerEnsureNullableArrayRejectsInvalidValues
     * @param bool|stdClass|string $value
     * @return void
     * @psalm-param mixed $value
     */
    public function testEnsureNullableArrayRejectsInvalidValues($value)
    {
        $this->expectException(UnexpectedValueException::class);

        self::ensureNullableArray($value);
    }

    /**
     * @return array<string, array{bool|stdClass|string}>
     */
    public static function providerEnsureNullableArrayRejectsInvalidValues()
    {
        return [
            'case 1' => [false],
            'case 2' => ['array'],
            'case 3' => [new stdClass()],
        ];
    }

    /**
     * @dataProvider providerEnsureNonEmptyArrayReturnsExpectedValue
     * @param array{'key': null}|array{int} $value
     * @param array<array-key, null|int> $expected
     * @return void
     * @psalm-param mixed $value
     */
    public function testEnsureNonEmptyArrayReturnsExpectedValue($value, $expected)
    {
        $actual = self::ensureNonEmptyArray($value);

        $this->assertSame($expected, $actual);
    }

    /**
     * @return array<string, array{array{'key': null}|array{int}, array<array-key, null|int>}>
     */
    public static function providerEnsureNonEmptyArrayReturnsExpectedValue()
    {
        return [
            'case 1' => [['key' => null], ['key' => null]],
            'case 2' => [[1], [1]],
        ];
    }

    /**
     * @dataProvider providerEnsureNonEmptyArrayRejectsInvalidValues
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
    public static function providerEnsureNonEmptyArrayRejectsInvalidValues()
    {
        return [
            'case 1' => [[]],
            'case 2' => [null],
            'case 3' => ['array'],
        ];
    }

    /**
     * @dataProvider providerEnsureArrayHasKeyReturnsExpectedValue
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
    public static function providerEnsureArrayHasKeyReturnsExpectedValue()
    {
        return [
            'case 1' => [['key' => null], 'key', ['key' => null]],
            'case 2' => [[0 => false], 0, [0 => false]],
        ];
    }

    /**
     * @dataProvider providerEnsureArrayHasKeyRejectsInvalidValues
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
    public static function providerEnsureArrayHasKeyRejectsInvalidValues()
    {
        return [
            'case 1' => [[], 'key'],
            'case 2' => [['other' => 1], 'key'],
        ];
    }

    /**
     * @dataProvider providerEnsureStringArrayReturnsExpectedValue
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
    public static function providerEnsureStringArrayReturnsExpectedValue()
    {
        return [
            'case 1' => [[], []],
            'case 2' => [[2 => 'a', 8 => 'b'], [2 => 'a', 8 => 'b']],
            'case 3' => [['key' => 'value'], ['key' => 'value']],
        ];
    }

    /**
     * @dataProvider providerEnsureStringArrayRejectsInvalidValues
     * @param array{int}|array{string, bool}|bool $value
     * @return void
     * @psalm-param mixed $value
     */
    public function testEnsureStringArrayRejectsInvalidValues($value)
    {
        $this->expectException(UnexpectedValueException::class);

        self::ensureStringArray($value);
    }

    /**
     * @return array<string, array{array{int}|array{string, bool}|bool}>
     */
    public static function providerEnsureStringArrayRejectsInvalidValues()
    {
        return [
            'case 1' => [['a', false]],
            'case 2' => [false],
            'case 3' => [[1]],
        ];
    }

    /**
     * @dataProvider providerEnsureNullableStringArrayReturnsExpectedValue
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
    public static function providerEnsureNullableStringArrayReturnsExpectedValue()
    {
        return [
            'case 1' => [null, null],
            'case 2' => [[], []],
            'case 3' => [[2 => 'a'], [2 => 'a']],
        ];
    }

    /**
     * @dataProvider providerEnsureNullableStringArrayRejectsInvalidValues
     * @param array{string, bool}|bool $value
     * @return void
     * @psalm-param mixed $value
     */
    public function testEnsureNullableStringArrayRejectsInvalidValues($value)
    {
        $this->expectException(UnexpectedValueException::class);

        self::ensureNullableStringArray($value);
    }

    /**
     * @return array<string, array{array{string, bool}|bool}>
     */
    public static function providerEnsureNullableStringArrayRejectsInvalidValues()
    {
        return [
            'case 1' => [['a', false]],
            'case 2' => [false],
        ];
    }

    /**
     * @dataProvider providerEnsureNullableStringOrStringArrayReturnsExpectedValue
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
    public static function providerEnsureNullableStringOrStringArrayReturnsExpectedValue()
    {
        return [
            'case 1' => [null, null],
            'case 2' => ['echo', 'echo'],
            'case 3' => [[2 => 'a', 8 => 'b'], [2 => 'a', 8 => 'b']],
        ];
    }

    /**
     * @dataProvider providerEnsureNullableStringOrStringArrayRejectsInvalidValues
     * @param array{string, bool}|bool|int $value
     * @return void
     * @psalm-param mixed $value
     */
    public function testEnsureNullableStringOrStringArrayRejectsInvalidValues($value)
    {
        $this->expectException(UnexpectedValueException::class);

        self::ensureNullableStringOrStringArray($value);
    }

    /**
     * @return array<string, array{array{string, bool}|bool|int}>
     */
    public static function providerEnsureNullableStringOrStringArrayRejectsInvalidValues()
    {
        return [
            'case 1' => [['a', false]],
            'case 2' => [false],
            'case 3' => [1],
        ];
    }

    /**
     * @dataProvider providerEnsureStringOrArrayReturnsExpectedValue
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
    public static function providerEnsureStringOrArrayReturnsExpectedValue()
    {
        return [
            'case 1' => ['', ''],
            'case 2' => [[], []],
            'case 3' => [['key' => null], ['key' => null]],
        ];
    }

    /**
     * @dataProvider providerEnsureStringOrArrayRejectsInvalidValues
     * @param bool|null|stdClass $value
     * @return void
     * @psalm-param mixed $value
     */
    public function testEnsureStringOrArrayRejectsInvalidValues($value)
    {
        $this->expectException(UnexpectedValueException::class);

        self::ensureStringOrArray($value);
    }

    /**
     * @return array<string, array{bool|null|stdClass}>
     */
    public static function providerEnsureStringOrArrayRejectsInvalidValues()
    {
        return [
            'case 1' => [null],
            'case 2' => [false],
            'case 3' => [new stdClass()],
        ];
    }

    /**
     * @dataProvider providerEnsureStringKeyedArrayReturnsExpectedValue
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
    public static function providerEnsureStringKeyedArrayReturnsExpectedValue()
    {
        $value2 = ['key' => new stdClass()];

        return [
            'case 1' => [[], []],
            'case 2' => [['key' => null], ['key' => null]],
            'case 3' => [$value2, $value2],
        ];
    }

    /**
     * @dataProvider providerEnsureStringKeyedArrayRejectsInvalidValues
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
    public static function providerEnsureStringKeyedArrayRejectsInvalidValues()
    {
        return [
            'case 1' => [[0 => 1]],
            'case 2' => [false],
        ];
    }

    /**
     * @dataProvider providerEnsureStringMapReturnsExpectedValue
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
    public static function providerEnsureStringMapReturnsExpectedValue()
    {
        return [
            'case 1' => [[], []],
            'case 2' => [['key' => ''], ['key' => '']],
            'case 3' => [['key' => 'value'], ['key' => 'value']],
        ];
    }

    /**
     * @dataProvider providerEnsureStringMapRejectsInvalidValues
     * @param array{'key': bool}|array{string}|bool $value
     * @return void
     * @psalm-param mixed $value
     */
    public function testEnsureStringMapRejectsInvalidValues($value)
    {
        $this->expectException(UnexpectedValueException::class);

        self::ensureStringMap($value);
    }

    /**
     * @return array<string, array{array{'key': bool}|array{string}|bool}>
     */
    public static function providerEnsureStringMapRejectsInvalidValues()
    {
        return [
            'case 1' => [['key' => false]],
            'case 2' => [[0 => 'value']],
            'case 3' => [false],
        ];
    }

    /**
     * @return array<string, array{array{'key': bool}|array{string}|bool}>
     */
    public static function providerEnsureStringMapUsesProvidedMessage()
    {
        return [
            'case 1' => [['key' => false]],
            'case 2' => [[0 => 'value']],
            'case 3' => [false],
        ];
    }

    /**
     * @dataProvider providerEnsureNullableStringMapReturnsExpectedValue
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
    public static function providerEnsureNullableStringMapReturnsExpectedValue()
    {
        return [
            'case 1' => [null, null],
            'case 2' => [[], []],
            'case 3' => [['key' => 'value'], ['key' => 'value']],
        ];
    }

    /**
     * @dataProvider providerEnsureNullableStringMapRejectsInvalidValues
     * @param array{'key': bool}|bool $value
     * @return void
     * @psalm-param mixed $value
     */
    public function testEnsureNullableStringMapRejectsInvalidValues($value)
    {
        $this->expectException(UnexpectedValueException::class);

        self::ensureNullableStringMap($value);
    }

    /**
     * @return array<string, array{array{'key': bool}|bool}>
     */
    public static function providerEnsureNullableStringMapRejectsInvalidValues()
    {
        return [
            'case 1' => [['key' => false]],
            'case 2' => [false],
        ];
    }

    /**
     * @dataProvider providerEnsureNullableBoolOrStringReturnsExpectedValue
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
    public static function providerEnsureNullableBoolOrStringReturnsExpectedValue()
    {
        return [
            'case 1' => [null, null],
            'case 2' => [true, true],
            'case 3' => [false, false],
            'case 4' => ['value', 'value'],
        ];
    }

    /**
     * @dataProvider providerEnsureNullableBoolOrStringRejectsInvalidValues
     * @param array{}|int|stdClass $value
     * @return void
     * @psalm-param mixed $value
     */
    public function testEnsureNullableBoolOrStringRejectsInvalidValues($value)
    {
        $this->expectException(UnexpectedValueException::class);

        self::ensureNullableBoolOrString($value);
    }

    /**
     * @return array<string, array{array{}|int|stdClass}>
     */
    public static function providerEnsureNullableBoolOrStringRejectsInvalidValues()
    {
        return [
            'case 1' => [1],
            'case 2' => [[]],
            'case 3' => [new stdClass()],
        ];
    }

    /**
     * @dataProvider providerEnsureObjectReturnsExpectedValue
     * @param stdClass $value
     * @param stdClass $expected
     * @return void
     * @psalm-param mixed $value
     */
    public function testEnsureObjectReturnsExpectedValue($value, $expected)
    {
        $actual = self::ensureObject($value);

        $this->assertSame($expected, $actual);
    }

    /**
     * @return array<string, array{stdClass, stdClass}>
     */
    public static function providerEnsureObjectReturnsExpectedValue()
    {
        $value0 = new stdClass();

        return [
            'case 1' => [$value0, $value0],
        ];
    }

    /**
     * @dataProvider providerEnsureObjectRejectsInvalidValues
     * @param array{}|null|string $value
     * @return void
     * @psalm-param mixed $value
     */
    public function testEnsureObjectRejectsInvalidValues($value)
    {
        $this->expectException(UnexpectedValueException::class);

        self::ensureObject($value);
    }

    /**
     * @return array<string, array{array{}|null|string}>
     */
    public static function providerEnsureObjectRejectsInvalidValues()
    {
        return [
            'case 1' => [null],
            'case 2' => [[]],
            'case 3' => ['object'],
        ];
    }

    /**
     * @dataProvider providerEnsureClassStringReturnsExpectedValue
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
    public static function providerEnsureClassStringReturnsExpectedValue()
    {
        return [
            'case 1' => [stdClass::class, stdClass::class],
            'case 2' => [Traversable::class, Traversable::class],
        ];
    }

    /**
     * @dataProvider providerEnsureClassStringRejectsInvalidValues
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
    public static function providerEnsureClassStringRejectsInvalidValues()
    {
        return [
            'case 1' => ['ClassThatDoesNotExist'],
            'case 2' => [false],
            'case 3' => [new stdClass()],
        ];
    }

    /**
     * @dataProvider providerEnsureInstanceOfReturnsExpectedValue
     * @param stdClass $value
     * @param class-string   $class
     * @param stdClass $expected
     * @return void
     */
    public function testEnsureInstanceOfReturnsExpectedValue($value, $class, $expected)
    {
        $actual = self::ensureInstanceOf($value, $class);

        $this->assertSame($expected, $actual);
    }

    /**
     * @return array<string, array{stdClass, class-string, stdClass}>
     */
    public static function providerEnsureInstanceOfReturnsExpectedValue()
    {
        $value0 = new stdClass();

        return [
            'case 1' => [$value0, stdClass::class, $value0],
        ];
    }

    /**
     * @dataProvider providerEnsureInstanceOfRejectsInvalidValues
     * @param array{}|null|stdClass $value
     * @param class-string                $class
     * @return void
     */
    public function testEnsureInstanceOfRejectsInvalidValues($value, $class)
    {
        $this->expectException(UnexpectedValueException::class);

        self::ensureInstanceOf($value, $class);
    }

    /**
     * @return array<string, array{array{}|null|stdClass, class-string}>
     */
    public static function providerEnsureInstanceOfRejectsInvalidValues()
    {
        return [
            'case 1' => [null, stdClass::class],
            'case 2' => [[], stdClass::class],
            'case 3' => [new stdClass(), Traversable::class],
        ];
    }

    /**
     * @dataProvider providerEnsureNullableInstanceOfReturnsExpectedValue
     * @param null|stdClass $value
     * @param class-string        $class
     * @param null|stdClass $expected
     * @return void
     */
    public function testEnsureNullableInstanceOfReturnsExpectedValue($value, $class, $expected)
    {
        $actual = self::ensureNullableInstanceOf($value, $class);

        $this->assertSame($expected, $actual);
    }

    /**
     * @return array<string, array{null|stdClass, class-string, null|stdClass}>
     */
    public static function providerEnsureNullableInstanceOfReturnsExpectedValue()
    {
        $value1 = new stdClass();

        return [
            'case 1' => [null, stdClass::class, null],
            'case 2' => [$value1, stdClass::class, $value1],
        ];
    }

    /**
     * @dataProvider providerEnsureNullableInstanceOfRejectsInvalidValues
     * @param array{}|bool $value
     * @param class-string       $class
     * @return void
     * @psalm-param mixed $value
     */
    public function testEnsureNullableInstanceOfRejectsInvalidValues($value, $class)
    {
        $this->expectException(UnexpectedValueException::class);

        self::ensureNullableInstanceOf($value, $class);
    }

    /**
     * @return array<string, array{array{}|bool, class-string}>
     */
    public static function providerEnsureNullableInstanceOfRejectsInvalidValues()
    {
        return [
            'case 1' => [[], stdClass::class],
            'case 2' => [false, stdClass::class],
        ];
    }

    /**
     * @dataProvider providerEnsureNonEmptyStringOrInstanceOfReturnsExpectedValue
     * @param stdClass|string $value
     * @param class-string          $class
     * @param stdClass|string $expected
     * @return void
     */
    public function testEnsureNonEmptyStringOrInstanceOfReturnsExpectedValue($value, $class, $expected)
    {
        $actual = self::ensureNonEmptyStringOrInstanceOf($value, $class);

        $this->assertSame($expected, $actual);
    }

    /**
     * @return array<string, array{stdClass|string, class-string, stdClass|string}>
     */
    public static function providerEnsureNonEmptyStringOrInstanceOfReturnsExpectedValue()
    {
        $value1 = new stdClass();

        return [
            'case 1' => ['value', stdClass::class, 'value'],
            'case 2' => [$value1, stdClass::class, $value1],
        ];
    }

    /**
     * @dataProvider providerEnsureNonEmptyStringOrInstanceOfRejectsInvalidValues
     * @param bool|null|string $value
     * @param class-string           $class
     * @return void
     */
    public function testEnsureNonEmptyStringOrInstanceOfRejectsInvalidValues($value, $class)
    {
        $this->expectException(UnexpectedValueException::class);

        self::ensureNonEmptyStringOrInstanceOf($value, $class);
    }

    /**
     * @return array<string, array{bool|null|string, class-string}>
     */
    public static function providerEnsureNonEmptyStringOrInstanceOfRejectsInvalidValues()
    {
        return [
            'case 1' => ['', stdClass::class],
            'case 2' => [null, stdClass::class],
            'case 3' => [false, stdClass::class],
        ];
    }

    /**
     * @dataProvider providerEnsureStringOrArrayOrInstanceOfReturnsExpectedValue
     * @param array{}|stdClass|string $value
     * @param class-string                  $class
     * @param array{}|stdClass|string $expected
     * @return void
     */
    public function testEnsureStringOrArrayOrInstanceOfReturnsExpectedValue($value, $class, $expected)
    {
        $actual = self::ensureStringOrArrayOrInstanceOf($value, $class);

        $this->assertSame($expected, $actual);
    }

    /**
     * @return array<string, array{array{}|stdClass|string, class-string, array{}|stdClass|string}>
     */
    public static function providerEnsureStringOrArrayOrInstanceOfReturnsExpectedValue()
    {
        $value2 = new stdClass();

        return [
            'case 1' => ['value', stdClass::class, 'value'],
            'case 2' => [[], stdClass::class, []],
            'case 3' => [$value2, stdClass::class, $value2],
        ];
    }

    /**
     * @dataProvider providerEnsureStringOrArrayOrInstanceOfRejectsInvalidValues
     * @param bool|null $value
     * @param class-string    $class
     * @return void
     * @psalm-param mixed $value
     */
    public function testEnsureStringOrArrayOrInstanceOfRejectsInvalidValues($value, $class)
    {
        $this->expectException(UnexpectedValueException::class);

        self::ensureStringOrArrayOrInstanceOf($value, $class);
    }

    /**
     * @return array<string, array{bool|null, class-string}>
     */
    public static function providerEnsureStringOrArrayOrInstanceOfRejectsInvalidValues()
    {
        return [
            'case 1' => [false, stdClass::class],
            'case 2' => [null, stdClass::class],
        ];
    }

    /**
     * @dataProvider providerEnsureNullReturnsExpectedValue
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
    public static function providerEnsureNullReturnsExpectedValue()
    {
        return [
            'case 1' => [null, null],
        ];
    }

    /**
     * @dataProvider providerEnsureNullRejectsInvalidValues
     * @param array{}|bool|int|string $value
     * @return void
     * @psalm-param mixed $value
     */
    public function testEnsureNullRejectsInvalidValues($value)
    {
        $this->expectException(UnexpectedValueException::class);

        self::ensureNull($value);
    }

    /**
     * @return array<string, array{array{}|bool|int|string}>
     */
    public static function providerEnsureNullRejectsInvalidValues()
    {
        return [
            'case 1' => [false],
            'case 2' => [0],
            'case 3' => [''],
            'case 4' => [[]],
        ];
    }

    /**
     * @dataProvider providerEnsureOneOfReturnsExpectedValue
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
    public static function providerEnsureOneOfReturnsExpectedValue()
    {
        return [
            'case 1' => ['one', ['one', 'two'], 'one'],
        ];
    }

    /**
     * @dataProvider providerEnsureOneOfRejectsInvalidValues
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
    public static function providerEnsureOneOfRejectsInvalidValues()
    {
        return [
            'case 1' => ['three', ['one', 'two']],
            'case 2' => [1, ['1']],
        ];
    }

    /**
     * @dataProvider providerEnsureMatchesReturnsExpectedValue
     * @param string $value
     * @param non-empty-string $pattern
     * @param string $expected
     * @return void
     * @psalm-param mixed $value
     */
    public function testEnsureMatchesReturnsExpectedValue($value, $pattern, $expected)
    {
        $actual = self::ensureMatches($value, $pattern);

        $this->assertSame($expected, $actual);
    }

    /**
     * @return array<string, array{string, non-empty-string, string}>
     */
    public static function providerEnsureMatchesReturnsExpectedValue()
    {
        return [
            'case 1' => ['123', '/^[0-9]+$/', '123'],
        ];
    }

    /**
     * @dataProvider providerEnsureMatchesRejectsInvalidValues
     * @param string $value
     * @param non-empty-string $pattern
     * @return void
     * @psalm-param mixed $value
     */
    public function testEnsureMatchesRejectsInvalidValues($value, $pattern)
    {
        $this->expectException(InvalidFormatException::class);

        self::ensureMatches($value, $pattern);
    }

    /**
     * @return array<string, array{string, non-empty-string}>
     */
    public static function providerEnsureMatchesRejectsInvalidValues()
    {
        return [
            'case 1' => ['123x', '/^[0-9]+$/'],
        ];
    }

    /**
     * @dataProvider providerEnsureNullableStreamReturnsExpectedValue
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
    public static function providerEnsureNullableStreamReturnsExpectedValue()
    {
        $value1 = fopen('php://memory', 'w+');
        self::assertNotFalse($value1);

        return [
            'case 1' => [null, null],
            'case 2' => [$value1, $value1],
        ];
    }

    /**
     * @dataProvider providerEnsureNullableStreamRejectsInvalidValues
     * @param bool|stdClass|string $value
     * @return void
     * @psalm-param mixed $value
     */
    public function testEnsureNullableStreamRejectsInvalidValues($value)
    {
        $this->expectException(UnexpectedValueException::class);

        self::ensureNullableStream($value);
    }

    /**
     * @return array<string, array{bool|stdClass|string}>
     */
    public static function providerEnsureNullableStreamRejectsInvalidValues()
    {
        return [
            'case 1' => [false],
            'case 2' => ['stream'],
            'case 3' => [new stdClass()],
        ];
    }

    /**
     * @dataProvider providerEnsureStreamReturnsExpectedValue
     * @param resource $value
     * @param resource $expected
     * @return void
     * @psalm-param mixed $value
     */
    public function testEnsureStreamReturnsExpectedValue($value, $expected)
    {
        $actual = self::ensureStream($value);

        $this->assertSame($expected, $actual);
    }

    /**
     * @return array<string, array{resource, resource}>
     */
    public static function providerEnsureStreamReturnsExpectedValue()
    {
        $value0 = fopen('php://memory', 'w+');
        self::assertNotFalse($value0);

        return [
            'case 1' => [$value0, $value0],
        ];
    }

    /**
     * @dataProvider providerEnsureStreamRejectsInvalidValues
     * @param bool|null|stdClass|string $value
     * @return void
     * @psalm-param mixed $value
     */
    public function testEnsureStreamRejectsInvalidValues($value)
    {
        $this->expectException(UnexpectedValueException::class);

        self::ensureStream($value);
    }

    /**
     * @return array<string, array{bool|null|stdClass|string}>
     */
    public static function providerEnsureStreamRejectsInvalidValues()
    {
        return [
            'case 1' => [null],
            'case 2' => ['php://memory'],
            'case 3' => [false],
            'case 4' => [new stdClass()],
        ];
    }

    /**
     * @dataProvider providerEnsureNullableScalarOrStreamOrTraversableReturnsExpectedValue
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
    public static function providerEnsureNullableScalarOrStreamOrTraversableReturnsExpectedValue()
    {
        $value4 = fopen('php://memory', 'w+');
        self::assertNotFalse($value4);
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
     * @dataProvider providerEnsureNullableScalarOrStreamOrTraversableRejectsInvalidValues
     * @param array{}|stdClass $value
     * @return void
     * @psalm-param mixed $value
     */
    public function testEnsureNullableScalarOrStreamOrTraversableRejectsInvalidValues($value)
    {
        $this->expectException(UnexpectedValueException::class);

        self::ensureNullableScalarOrStreamOrTraversable($value);
    }

    /**
     * @return array<string, array{array{}|stdClass}>
     */
    public static function providerEnsureNullableScalarOrStreamOrTraversableRejectsInvalidValues()
    {
        return [
            'case 1' => [[]],
            'case 2' => [new stdClass()],
        ];
    }

    /**
     * @dataProvider providerEnsureClassStringOfReturnsExpectedValue
     * @param string $value
     * @param class-string $class
     * @param string $expected
     * @return void
     */
    public function testEnsureClassStringOfReturnsExpectedValue($value, $class, $expected)
    {
        $actual = self::ensureClassStringOf($value, $class);

        $this->assertSame($expected, $actual);
    }

    /**
     * @return array<string, array{string, class-string, string}>
     */
    public static function providerEnsureClassStringOfReturnsExpectedValue()
    {
        return [
            'case 1' => [ArrayIterator::class, Traversable::class, ArrayIterator::class],
        ];
    }

    /**
     * @dataProvider providerEnsureClassStringOfRejectsInvalidValues
     * @param bool|string $value
     * @param class-string      $class
     * @return void
     */
    public function testEnsureClassStringOfRejectsInvalidValues($value, $class)
    {
        $this->expectException(UnexpectedValueException::class);

        self::ensureClassStringOf($value, $class);
    }

    /**
     * @return array<string, array{bool|string, class-string}>
     */
    public static function providerEnsureClassStringOfRejectsInvalidValues()
    {
        return [
            'case 1' => [stdClass::class, Traversable::class],
            'case 2' => ['MissingClass', Traversable::class],
            'case 3' => [false, Traversable::class],
        ];
    }

    /**
     * @dataProvider providerEnsureClassStringOrInstanceOfReturnsExpectedValue
     * @param ArrayIterator<never, never>|string $value
     * @param class-string                             $class
     * @param ArrayIterator<never, never>|string $expected
     * @return void
     */
    public function testEnsureClassStringOrInstanceOfReturnsExpectedValue($value, $class, $expected)
    {
        $actual = self::ensureClassStringOrInstanceOf($value, $class);

        $this->assertSame($expected, $actual);
    }

    /**
     * @return array<string, array{ArrayIterator<never, never>|string, class-string, ArrayIterator<never, never>|string}>
     */
    public static function providerEnsureClassStringOrInstanceOfReturnsExpectedValue()
    {
        $value1 = new ArrayIterator([]);

        return [
            'case 1' => [ArrayIterator::class, Traversable::class, ArrayIterator::class],
            'case 2' => [$value1, Traversable::class, $value1],
        ];
    }

    /**
     * @dataProvider providerEnsureClassStringOrInstanceOfRejectsInvalidValues
     * @param bool|stdClass|string $value
     * @param class-string               $class
     * @return void
     */
    public function testEnsureClassStringOrInstanceOfRejectsInvalidValues($value, $class)
    {
        $this->expectException(UnexpectedValueException::class);

        self::ensureClassStringOrInstanceOf($value, $class);
    }

    /**
     * @return array<string, array{bool|stdClass|string, class-string}>
     */
    public static function providerEnsureClassStringOrInstanceOfRejectsInvalidValues()
    {
        return [
            'case 1' => [stdClass::class, Traversable::class],
            'case 2' => [new stdClass(), Traversable::class],
            'case 3' => [false, Traversable::class],
        ];
    }

    /**
     * @dataProvider providerEnsureScalarOrStringableReturnsExpectedValue
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
    public static function providerEnsureScalarOrStringableReturnsExpectedValue()
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
     * @dataProvider providerEnsureScalarOrStringableRejectsInvalidValues
     * @param array{}|null|stdClass $value
     * @return void
     * @psalm-param mixed $value
     */
    public function testEnsureScalarOrStringableRejectsInvalidValues($value)
    {
        $this->expectException(UnexpectedValueException::class);

        self::ensureScalarOrStringable($value);
    }

    /**
     * @return array<string, array{array{}|null|stdClass}>
     */
    public static function providerEnsureScalarOrStringableRejectsInvalidValues()
    {
        return [
            'case 1' => [null],
            'case 2' => [[]],
            'case 3' => [new stdClass()],
        ];
    }

    /**
     * @dataProvider providerEnsureNullableScalarOrStringableOrArrayReturnsExpectedValue
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
    public static function providerEnsureNullableScalarOrStringableOrArrayReturnsExpectedValue()
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
     * @dataProvider providerEnsureNullableScalarOrStringableOrArrayRejectsInvalidValues
     * @param array{'bad': stdClass}|array{array{int}}|stdClass $value
     * @return void
     * @psalm-param mixed $value
     */
    public function testEnsureNullableScalarOrStringableOrArrayRejectsInvalidValues($value)
    {
        $this->expectException(UnexpectedValueException::class);

        self::ensureNullableScalarOrStringableOrArray($value);
    }

    /**
     * @return array<string, array{array{'bad': stdClass}|array{array{int}}|stdClass}>
     */
    public static function providerEnsureNullableScalarOrStringableOrArrayRejectsInvalidValues()
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
     * @dataProvider providerEnsureStringMapUsesProvidedMessage
     * @param array{'key': bool}|array{string}|bool $value
     * @return void
     * @psalm-param mixed $value
     */
    public function testEnsureStringMapUsesProvidedMessage($value)
    {
        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('A custom validation message');

        self::ensureStringMap($value, 'A custom validation message');
    }
}
