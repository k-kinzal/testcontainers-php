<?php

namespace Testcontainers\Utility;

use Stringable as NativeStringable;
use Testcontainers\Exceptions\InvalidFormatException;
use Traversable;
use UnexpectedValueException;

/**
 * Ensure the expected type and return the supplied value.
 */
trait Ensure
{
    /**
     * Ensure that the value is a string.
     *
     * @psalm-assert string $value
     *
     * @param string      $value   the input value
     * @param null|string $message an optional error message
     * @psalm-param mixed $value
     *
     * @return string
     *
     * @throws UnexpectedValueException if the value does not satisfy the constraint
     */
    protected static function ensureString($value, $message = null)
    {
        if (!is_string($value)) {
            throw new UnexpectedValueException($message === null ? 'Expected a string' : $message);
        }

        return $value;
    }

    /**
     * Ensure that the value is a boolean.
     *
     * @psalm-assert bool $value
     *
     * @param bool        $value   the input value
     * @param null|string $message an optional error message
     * @psalm-param mixed $value
     *
     * @return bool
     *
     * @throws UnexpectedValueException if the value does not satisfy the constraint
     */
    protected static function ensureBool($value, $message = null)
    {
        if (!is_bool($value)) {
            throw new UnexpectedValueException($message === null ? 'Expected a boolean' : $message);
        }

        return $value;
    }

    /**
     * Ensure that the value is an integer.
     *
     * @psalm-assert int $value
     *
     * @param int         $value   the input value
     * @param null|string $message an optional error message
     * @psalm-param mixed $value
     *
     * @return int
     *
     * @throws UnexpectedValueException if the value does not satisfy the constraint
     */
    protected static function ensureInt($value, $message = null)
    {
        if (!is_int($value)) {
            throw new UnexpectedValueException($message === null ? 'Expected an integer' : $message);
        }

        return $value;
    }

    /**
     * Ensure that the value is an array.
     *
     * @psalm-assert array<array-key, mixed> $value
     *
     * @param array<array-key, mixed> $value   the input value
     * @param null|string             $message an optional error message
     * @psalm-param mixed $value
     *
     * @return array<array-key, mixed>
     *
     * @throws UnexpectedValueException if the value does not satisfy the constraint
     */
    protected static function ensureArray($value, $message = null)
    {
        if (!is_array($value)) {
            throw new UnexpectedValueException($message === null ? 'Expected an array' : $message);
        }

        return $value;
    }

    /**
     * Ensure that the value is an object.
     *
     * @psalm-assert object $value
     *
     * @param object      $value   the input value
     * @param null|string $message an optional error message
     * @psalm-param mixed $value
     *
     * @return object
     *
     * @throws UnexpectedValueException if the value does not satisfy the constraint
     */
    protected static function ensureObject($value, $message = null)
    {
        if (!is_object($value)) {
            throw new UnexpectedValueException($message === null ? 'Expected an object' : $message);
        }

        return $value;
    }

    /**
     * Ensure that the value contains only decimal digits.
     *
     * @psalm-assert numeric-string $value
     *
     * @param numeric-string $value   the input value
     * @param null|string    $message an optional error message
     * @psalm-param mixed $value
     *
     * @return numeric-string
     *
     * @throws UnexpectedValueException if the value does not satisfy the constraint
     */
    protected static function ensureDigitString($value, $message = null)
    {
        if (!is_string($value) || !is_numeric($value) || strspn($value, '0123456789') !== strlen($value)) {
            throw new UnexpectedValueException($message === null ? 'Expected a string of decimal digits' : $message);
        }

        return $value;
    }

    /**
     * Ensure that the value is a finite, non-negative number.
     *
     * @psalm-assert float|int $value
     *
     * @param float|int   $value   the input value
     * @param null|string $message an optional error message
     * @psalm-param mixed $value
     *
     * @return float|int
     *
     * @throws UnexpectedValueException if the value does not satisfy the constraint
     */
    protected static function ensureNonNegativeNumber($value, $message = null)
    {
        if ((!is_int($value) && !is_float($value)) || !is_finite((float) $value) || $value < 0) {
            throw new UnexpectedValueException($message === null ? 'Expected a finite, non-negative number' : $message);
        }

        return $value;
    }

    /**
     * Ensure that the value is a non-empty string.
     *
     * @psalm-assert non-empty-string $value
     *
     * @param non-empty-string $value   the input value
     * @param null|string      $message an optional error message
     * @psalm-param mixed $value
     *
     * @return non-empty-string
     *
     * @throws UnexpectedValueException if the value does not satisfy the constraint
     */
    protected static function ensureNonEmptyString($value, $message = null)
    {
        self::ensureString($value, $message);

        if ($value === '') {
            throw new UnexpectedValueException($message === null ? 'Expected a non-empty string' : $message);
        }

        return $value;
    }

    /**
     * Ensure that the value is null or string.
     *
     * @psalm-assert null|string $value
     *
     * @param null|string $value   the input value
     * @param null|string $message an optional error message
     * @psalm-param mixed $value
     *
     * @return null|string
     *
     * @throws UnexpectedValueException if the value does not satisfy the constraint
     */
    protected static function ensureNullableString($value, $message = null)
    {
        if ($value !== null) {
            self::ensureString($value, $message);
        }

        return $value;
    }

    /**
     * Ensure that the value is null or bool.
     *
     * @psalm-assert null|bool $value
     *
     * @param null|bool   $value   the input value
     * @param null|string $message an optional error message
     * @psalm-param mixed $value
     *
     * @return null|bool
     *
     * @throws UnexpectedValueException if the value does not satisfy the constraint
     */
    protected static function ensureNullableBool($value, $message = null)
    {
        if ($value !== null) {
            self::ensureBool($value, $message);
        }

        return $value;
    }

    /**
     * Ensure that the value is null or array<array-key, mixed>.
     *
     * @psalm-assert null|array<array-key, mixed> $value
     *
     * @param null|array<array-key, mixed> $value   the input value
     * @param null|string                  $message an optional error message
     * @psalm-param mixed $value
     *
     * @return null|array<array-key, mixed>
     *
     * @throws UnexpectedValueException if the value does not satisfy the constraint
     */
    protected static function ensureNullableArray($value, $message = null)
    {
        if ($value !== null) {
            self::ensureArray($value, $message);
        }

        return $value;
    }

    /**
     * Ensure that the value is null or float|int.
     *
     * @psalm-assert null|float|int $value
     *
     * @param null|float|int $value   the input value
     * @param null|string    $message an optional error message
     * @psalm-param mixed $value
     *
     * @return null|float|int
     *
     * @throws UnexpectedValueException if the value does not satisfy the constraint
     */
    protected static function ensureNullableNonNegativeNumber($value, $message = null)
    {
        if ($value !== null) {
            self::ensureNonNegativeNumber($value, $message);
        }

        return $value;
    }

    /**
     * Ensure that the value is null or non-empty-string.
     *
     * @psalm-assert null|non-empty-string $value
     *
     * @param null|non-empty-string $value   the input value
     * @param null|string           $message an optional error message
     * @psalm-param mixed $value
     *
     * @return null|non-empty-string
     *
     * @throws UnexpectedValueException if the value does not satisfy the constraint
     */
    protected static function ensureNullableNonEmptyString($value, $message = null)
    {
        if ($value !== null) {
            self::ensureNonEmptyString($value, $message);
        }

        return $value;
    }

    /**
     * Ensure that the value is an integer or a string of decimal digits.
     *
     * @psalm-assert int|numeric-string $value
     *
     * @param int|numeric-string $value   the input value
     * @param null|string        $message an optional error message
     * @psalm-param mixed $value
     *
     * @return int|numeric-string
     *
     * @throws UnexpectedValueException if the value does not satisfy the constraint
     */
    protected static function ensureIntOrDigitString($value, $message = null)
    {
        if (!is_int($value)) {
            self::ensureDigitString($value, $message);
        }

        return $value;
    }

    /**
     * Ensure that the value is within the inclusive integer bounds.
     *
     * @psalm-assert int $value
     *
     * @param int         $value   the input value
     * @param int         $minimum the inclusive lower bound
     * @param null|int    $maximum the inclusive upper bound, or null for no upper bound
     * @param null|string $message an optional error message
     * @psalm-param mixed $value
     *
     * @return int
     *
     * @throws UnexpectedValueException if the value does not satisfy the constraint
     */
    protected static function ensureIntRange($value, $minimum, $maximum = null, $message = null)
    {
        self::ensureInt($value, $message);

        if ($value < $minimum || ($maximum !== null && $value > $maximum)) {
            throw new UnexpectedValueException($message === null ? 'Value is outside the allowed range' : $message);
        }

        return $value;
    }

    /**
     * Ensure that the value is within the inclusive integer bounds.
     *
     * @psalm-assert int|numeric-string $value
     *
     * @param int|numeric-string $value   the input value
     * @param int                $minimum the inclusive lower bound
     * @param null|int           $maximum the inclusive upper bound, or null for no upper bound
     * @param null|string        $message an optional error message
     * @psalm-param mixed $value
     *
     * @return int|numeric-string
     *
     * @throws UnexpectedValueException if the value does not satisfy the constraint
     */
    protected static function ensureIntOrDigitStringRange($value, $minimum, $maximum = null, $message = null)
    {
        self::ensureIntOrDigitString($value, $message);

        if ($value < $minimum || ($maximum !== null && $value > $maximum)) {
            throw new UnexpectedValueException($message === null ? 'Value is outside the allowed range' : $message);
        }

        return $value;
    }

    /**
     * Ensure that the value is null or within the inclusive integer bounds.
     *
     * @psalm-assert null|int $value
     *
     * @param null|int    $value   the input value
     * @param int         $minimum the inclusive lower bound
     * @param null|int    $maximum the inclusive upper bound, or null for no upper bound
     * @param null|string $message an optional error message
     * @psalm-param mixed $value
     *
     * @return null|int
     *
     * @throws UnexpectedValueException if the value does not satisfy the constraint
     */
    protected static function ensureNullableIntRange($value, $minimum, $maximum = null, $message = null)
    {
        if ($value !== null) {
            self::ensureIntRange($value, $minimum, $maximum, $message);
        }

        return $value;
    }

    /**
     * Ensure that the value is a string or an array.
     *
     * @psalm-assert string|array<array-key, mixed> $value
     *
     * @param string|array<array-key, mixed> $value   the input value
     * @param null|string                    $message an optional error message
     * @psalm-param mixed $value
     *
     * @return string|array<array-key, mixed>
     *
     * @throws UnexpectedValueException if the value does not satisfy the constraint
     */
    protected static function ensureStringOrArray($value, $message = null)
    {
        if (!is_string($value)) {
            self::ensureArray($value, $message);
        }

        return $value;
    }

    /**
     * Ensure that every array value is a string, without changing its keys.
     *
     * @psalm-assert array<array-key, string> $value
     *
     * @param array<array-key, string> $value   the input value
     * @param null|string              $message an optional error message
     * @psalm-param mixed $value
     *
     * @return array<array-key, string>
     *
     * @throws UnexpectedValueException if the value does not satisfy the constraint
     */
    protected static function ensureStringArray($value, $message = null)
    {
        self::ensureArray($value, $message);

        $result = [];
        foreach ($value as $key => $item) {
            $item = self::ensureString($item, $message);

            $result[$key] = $item;
        }

        return $result;
    }

    /**
     * Ensure that the value is null or an array of strings.
     *
     * @psalm-assert null|array<array-key, string> $value
     *
     * @param null|array<array-key, string> $value   the input value
     * @param null|string                   $message an optional error message
     * @psalm-param mixed $value
     *
     * @return null|array<array-key, string>
     *
     * @throws UnexpectedValueException if the value does not satisfy the constraint
     */
    protected static function ensureNullableStringArray($value, $message = null)
    {
        if ($value !== null) {
            self::ensureStringArray($value, $message);
        }

        return $value;
    }

    /**
     * Ensure that the value is null, a string, or an array of strings.
     *
     * @psalm-assert null|string|array<array-key, string> $value
     *
     * @param null|string|array<array-key, string> $value   the input value
     * @param null|string                          $message an optional error message
     * @psalm-param mixed $value
     *
     * @return null|string|array<array-key, string>
     *
     * @throws UnexpectedValueException if the value does not satisfy the constraint
     */
    protected static function ensureNullableStringOrStringArray($value, $message = null)
    {
        if ($value !== null && !is_string($value)) {
            self::ensureStringArray($value, $message);
        }

        return $value;
    }

    /**
     * Ensure that the value is null, a boolean, or a string.
     *
     * @psalm-assert null|bool|string $value
     *
     * @param null|bool|string $value   the input value
     * @param null|string      $message an optional error message
     * @psalm-param mixed $value
     *
     * @return null|bool|string
     *
     * @throws UnexpectedValueException if the value does not satisfy the constraint
     */
    protected static function ensureNullableBoolOrString($value, $message = null)
    {
        if ($value !== null && !is_bool($value) && !is_string($value)) {
            throw new UnexpectedValueException($message === null ? 'Expected a boolean, string or null' : $message);
        }

        return $value;
    }

    /**
     * Ensure that every array key is a string.
     *
     * @psalm-assert array<string, mixed> $value
     *
     * @param array<string, mixed> $value   the input value
     * @param null|string          $message an optional error message
     * @psalm-param mixed $value
     *
     * @return array<string, mixed>
     *
     * @throws UnexpectedValueException if the value does not satisfy the constraint
     */
    protected static function ensureStringKeyedArray($value, $message = null)
    {
        self::ensureArray($value, $message);

        $result = [];
        foreach (array_keys($value) as $key) {
            $key = self::ensureString($key, $message);

            $result += [$key => $value[$key]];
        }

        return $result;
    }

    /**
     * Ensure that every array key and value is a string.
     *
     * @psalm-assert array<string, string> $value
     *
     * @param array<string, string> $value   the input value
     * @param null|string           $message an optional error message
     * @psalm-param mixed $value
     *
     * @return array<string, string>
     *
     * @throws UnexpectedValueException if the value does not satisfy the constraint
     */
    protected static function ensureStringMap($value, $message = null)
    {
        self::ensureStringKeyedArray($value, $message);
        self::ensureStringArray($value, $message);

        return $value;
    }

    /**
     * Ensure that the value is null or an array with string keys and values.
     *
     * @psalm-assert null|array<string, string> $value
     *
     * @param null|array<string, string> $value   the input value
     * @param null|string                $message an optional error message
     * @psalm-param mixed $value
     *
     * @return null|array<string, string>
     *
     * @throws UnexpectedValueException if the value does not satisfy the constraint
     */
    protected static function ensureNullableStringMap($value, $message = null)
    {
        if ($value !== null) {
            self::ensureStringMap($value, $message);
        }

        return $value;
    }

    /**
     * Ensure that the value is a non-empty array.
     *
     * @psalm-assert !empty $value
     *
     * @param non-empty-array<array-key, mixed> $value   the input value
     * @param null|string                       $message an optional error message
     * @psalm-param mixed $value
     *
     * @return non-empty-array<array-key, mixed>
     *
     * @throws UnexpectedValueException if the value does not satisfy the constraint
     */
    protected static function ensureNonEmptyArray($value, $message = null)
    {
        self::ensureArray($value, $message);

        if ($value === []) {
            throw new UnexpectedValueException($message === null ? 'Expected a non-empty array' : $message);
        }

        return $value;
    }

    /**
     * Ensure that the array contains the specified key.
     *
     * @param array<array-key, mixed> $value   the input value
     * @param array-key               $key     the required array key
     * @param null|string             $message an optional error message
     * @psalm-param mixed $value
     *
     * @return array<array-key, mixed>
     *
     * @throws UnexpectedValueException if the value does not satisfy the constraint
     */
    protected static function ensureArrayHasKey($value, $key, $message = null)
    {
        self::ensureArray($value, $message);

        if (!array_key_exists($key, $value)) {
            throw new UnexpectedValueException($message === null ? 'Expected the array to contain the required key' : $message);
        }

        return $value;
    }

    /**
     * Ensure that the value names an existing class or interface.
     *
     * @psalm-assert class-string $value
     *
     * @param class-string $value   the input value
     * @param null|string  $message an optional error message
     * @psalm-param mixed $value
     *
     * @return class-string
     *
     * @throws UnexpectedValueException if the value does not satisfy the constraint
     */
    protected static function ensureClassString($value, $message = null)
    {
        self::ensureString($value, $message);

        if (!class_exists($value) && !interface_exists($value)) {
            throw new UnexpectedValueException($message === null ? 'Expected an existing class or interface name' : $message);
        }

        return $value;
    }

    /**
     * Ensure that the value is an instance of the specified class.
     *
     * @template T of object
     *
     * @psalm-assert T $value
     *
     * @param T               $value   the input value
     * @param class-string<T> $class   the expected class or interface
     * @param null|string     $message an optional error message
     * @psalm-param mixed $value
     *
     * @return T
     *
     * @throws UnexpectedValueException if the value does not satisfy the constraint
     */
    protected static function ensureInstanceOf($value, $class, $message = null)
    {
        if (!$value instanceof $class) {
            throw new UnexpectedValueException($message === null ? 'Expected an instance of '.$class : $message);
        }

        return $value;
    }

    /**
     * Ensure that the value is null or an instance of the specified class.
     *
     * @template T of object
     *
     * @psalm-assert null|T $value
     *
     * @param null|T          $value   the input value
     * @param class-string<T> $class   the expected class or interface
     * @param null|string     $message an optional error message
     * @psalm-param mixed $value
     *
     * @return null|T
     *
     * @throws UnexpectedValueException if the value does not satisfy the constraint
     */
    protected static function ensureNullableInstanceOf($value, $class, $message = null)
    {
        if ($value !== null) {
            self::ensureInstanceOf($value, $class, $message);
        }

        return $value;
    }

    /**
     * Ensure that the value strictly matches one of the allowed values.
     *
     * @psalm-assert =string $value
     *
     * @param string                   $value   the input value
     * @param array<array-key, string> $values  the allowed values
     * @param null|string              $message an optional error message
     * @psalm-param mixed $value
     *
     * @return string
     *
     * @throws InvalidFormatException if the value does not satisfy the constraint
     */
    protected static function ensureOneOf($value, $values, $message = null)
    {
        if (!in_array($value, $values, true)) {
            throw new InvalidFormatException($value, $message === null ? $values : $message);
        }

        return $value;
    }

    /**
     * Ensure that the value is an open stream resource.
     *
     * @psalm-assert resource $value
     *
     * @param resource    $value   the input value
     * @param null|string $message an optional error message
     * @psalm-param mixed $value
     *
     * @return resource
     *
     * @throws UnexpectedValueException if the value does not satisfy the constraint
     */
    protected static function ensureStream($value, $message = null)
    {
        if (!is_resource($value) || get_resource_type($value) !== 'stream') {
            throw new UnexpectedValueException($message === null ? 'Expected an open stream resource' : $message);
        }

        return $value;
    }

    /**
     * Ensure that the value is null or an open stream resource.
     *
     * @psalm-assert null|resource $value
     *
     * @param null|resource $value   the input value
     * @param null|string   $message an optional error message
     * @psalm-param mixed $value
     *
     * @return null|resource
     *
     * @throws UnexpectedValueException if the value does not satisfy the constraint
     */
    protected static function ensureNullableStream($value, $message = null)
    {
        if ($value !== null) {
            self::ensureStream($value, $message);
        }

        return $value;
    }

    /**
     * Ensure that input is null, a scalar, an open stream, or traversable.
     *
     * @psalm-assert null|scalar|resource|Traversable $value
     *
     * @param null|scalar|resource|Traversable $value   the input value
     * @param null|string                      $message an optional error message
     * @psalm-param mixed $value
     *
     * @return null|scalar|resource|Traversable
     *
     * @throws UnexpectedValueException if the value does not satisfy the constraint
     */
    protected static function ensureNullableScalarOrStreamOrTraversable($value, $message = null)
    {
        if ($value !== null && !is_scalar($value) && !$value instanceof Traversable) {
            self::ensureStream($value, $message);
        }

        return $value;
    }

    /**
     * Ensure that the value is a non-empty string or an instance of the given class.
     *
     * @template T of object
     *
     * @psalm-assert non-empty-string|T $value
     *
     * @param non-empty-string|T $value   the input value
     * @param class-string<T>    $class   the allowed class or interface
     * @param null|string        $message an optional error message
     * @psalm-param mixed $value
     *
     * @return non-empty-string|T
     *
     * @throws UnexpectedValueException if the value does not satisfy the constraint
     */
    protected static function ensureNonEmptyStringOrInstanceOf($value, $class, $message = null)
    {
        if (is_string($value)) {
            self::ensureNonEmptyString($value, $message);
        } else {
            self::ensureInstanceOf($value, $class, $message);
        }

        return $value;
    }

    /**
     * Ensure that the value is a string, an array, or an instance of the given class.
     *
     * @template T of object
     *
     * @psalm-assert string|array<array-key, mixed>|T $value
     *
     * @param string|array<array-key, mixed>|T $value   the input value
     * @param class-string<T>                  $class   the allowed class or interface
     * @param null|string                      $message an optional error message
     * @psalm-param mixed $value
     *
     * @return string|array<array-key, mixed>|T
     *
     * @throws UnexpectedValueException if the value does not satisfy the constraint
     */
    protected static function ensureStringOrArrayOrInstanceOf($value, $class, $message = null)
    {
        if (!is_string($value) && !is_array($value)) {
            self::ensureInstanceOf($value, $class, $message);
        }

        return $value;
    }

    /**
     * Ensure that a string of decimal digits is within the inclusive bounds.
     *
     * @psalm-assert numeric-string $value
     *
     * @param numeric-string $value   the input value
     * @param int            $minimum the inclusive lower bound
     * @param null|int       $maximum the inclusive upper bound, or null for no upper bound
     * @param null|string    $message an optional error message
     * @psalm-param mixed $value
     *
     * @return numeric-string
     *
     * @throws UnexpectedValueException if the value does not satisfy the constraint
     */
    protected static function ensureDigitStringRange($value, $minimum, $maximum = null, $message = null)
    {
        self::ensureDigitString($value, $message);

        if ($value < $minimum || ($maximum !== null && $value > $maximum)) {
            throw new UnexpectedValueException($message === null ? 'Value is outside the allowed range' : $message);
        }

        return $value;
    }

    /**
     * Ensure that no value was supplied.
     *
     * @psalm-assert null $value
     *
     * @param null        $value   the input value
     * @param null|string $message an optional error message
     * @psalm-param mixed $value
     *
     * @return null
     *
     * @throws UnexpectedValueException if the value does not satisfy the constraint
     */
    protected static function ensureNull($value, $message = null)
    {
        if ($value !== null) {
            throw new UnexpectedValueException($message === null ? 'Expected null' : $message);
        }

        return $value;
    }

    /**
     * Ensure that a string matches the expected format.
     *
     * @psalm-assert string $value
     *
     * @param string           $value   the input value
     * @param non-empty-string $pattern the regular expression describing the format
     * @param null|string      $message an optional error message
     * @psalm-param mixed $value
     *
     * @return string
     *
     * @throws InvalidFormatException if the value does not satisfy the constraint
     */
    protected static function ensureMatches($value, $pattern, $message = null)
    {
        self::ensureString($value, $message);

        if (preg_match($pattern, $value) !== 1) {
            throw new InvalidFormatException($value, $message === null ? $pattern : $message);
        }

        return $value;
    }

    /**
     * Ensure that a scalar or object can be rendered as a string.
     *
     * @psalm-assert scalar|NativeStringable $value
     *
     * @param scalar|NativeStringable $value   the input value
     * @param null|string             $message an optional error message
     * @psalm-param mixed $value
     *
     * @return scalar|NativeStringable
     *
     * @throws UnexpectedValueException if the value cannot be rendered
     */
    protected static function ensureScalarOrStringable($value, $message = null)
    {
        if (!is_scalar($value) && !self::isStringable($value)) {
            throw new UnexpectedValueException($message === null ? 'Expected a scalar or stringable object' : $message);
        }

        return $value;
    }

    /**
     * Determine whether an object provides string conversion.
     *
     * @psalm-assert-if-true NativeStringable $value
     *
     * @param mixed $value the value to inspect
     *
     * @return bool
     */
    protected static function isStringable($value)
    {
        return is_object($value) && method_exists($value, '__toString');
    }

    /**
     * Ensure that a value is null, stringable, or an array of stringable values.
     *
     * @psalm-assert null|scalar|NativeStringable|array<array-key, scalar|NativeStringable> $value
     *
     * @param null|scalar|NativeStringable|array<array-key, scalar|NativeStringable> $value   the input value
     * @param null|string                                                            $message an optional error message
     * @psalm-param mixed $value
     *
     * @return null|scalar|NativeStringable|array<array-key, scalar|NativeStringable>
     *
     * @throws UnexpectedValueException if a value cannot be rendered
     */
    protected static function ensureNullableScalarOrStringableOrArray($value, $message = null)
    {
        if (is_array($value)) {
            $result = [];
            foreach ($value as $key => $item) {
                $item = self::ensureScalarOrStringable($item, $message);

                $result[$key] = $item;
            }

            return $result;
        } elseif ($value !== null) {
            self::ensureScalarOrStringable($value, $message);
        }

        return $value;
    }

    /**
     * Ensure that each string key maps to null, a scalar, a stringable object, or an array of stringable values.
     *
     * @psalm-assert array<string, null|scalar|NativeStringable|array<array-key, scalar|NativeStringable>> $value
     *
     * @param array<string, null|scalar|NativeStringable|array<array-key, scalar|NativeStringable>> $value the input value
     * @param null|string $message an optional error message
     * @psalm-param mixed $value
     *
     * @return array<string, null|scalar|NativeStringable|array<array-key, scalar|NativeStringable>>
     *
     * @throws UnexpectedValueException if a key is not a string or a value cannot be rendered
     */
    protected static function ensureNullableScalarOrStringableOrArrayMap($value, $message = null)
    {
        self::ensureStringKeyedArray($value, $message);

        $result = [];
        foreach ($value as $key => $item) {
            $item = self::ensureNullableScalarOrStringableOrArray($item, $message);

            $result[$key] = $item;
        }

        return $result;
    }

    /**
     * Ensure that a class name identifies an implementation of the expected type.
     *
     * @template T of object
     *
     * @psalm-assert class-string<T> $value
     *
     * @param class-string<T> $value   the supplied class name
     * @param class-string<T> $class   the required parent class or interface
     * @param null|string     $message an optional error message
     * @psalm-param mixed $value
     *
     * @return class-string<T>
     *
     * @throws UnexpectedValueException if the name is not a matching class
     */
    protected static function ensureClassStringOf($value, $class, $message = null)
    {
        self::ensureString($value, $message);

        if (!class_exists($value) || !is_a($value, $class, true)) {
            throw new UnexpectedValueException($message === null ? 'Expected a class implementing '.$class : $message);
        }

        return $value;
    }

    /**
     * Ensure that a value identifies or implements the expected object type.
     *
     * @template T of object
     *
     * @psalm-assert class-string<T>|T $value
     *
     * @param class-string<T>|T $value   the supplied class name or instance
     * @param class-string<T>   $class   the required parent class or interface
     * @param null|string       $message an optional error message
     * @psalm-param mixed $value
     *
     * @return class-string<T>|T
     *
     * @throws UnexpectedValueException if the value does not match the required type
     */
    protected static function ensureClassStringOrInstanceOf($value, $class, $message = null)
    {
        if (is_string($value)) {
            self::ensureClassStringOf($value, $class, $message);
        } else {
            self::ensureInstanceOf($value, $class, $message);
        }

        return $value;
    }
}
