<?php

namespace Testcontainers\Utility;

use Stringable as NativeStringable;
use Testcontainers\Exceptions\InvalidFormatException;
use Traversable;
use UnexpectedValueException;

/**
 * Validate untrusted values and refine their types without coercing them.
 */
trait Ensure
{
    /**
     * Ensure that the value is a string.
     *
     * @param string      $value   the value to validate
     * @param null|string $message an optional validation error message
     * @psalm-param mixed $value
     *
     * @throws UnexpectedValueException if the value does not satisfy the constraint
     *
     * @return void
     * @psalm-assert string $value
     */
    protected static function ensureString($value, $message = null)
    {
        if (!is_string($value)) {
            throw new UnexpectedValueException($message === null ? 'Expected a string' : $message);
        }
    }

    /**
     * Ensure that the value is a boolean.
     *
     * @param bool        $value   the value to validate
     * @param null|string $message an optional validation error message
     * @psalm-param mixed $value
     *
     * @throws UnexpectedValueException if the value does not satisfy the constraint
     *
     * @return void
     * @psalm-assert bool $value
     */
    protected static function ensureBool($value, $message = null)
    {
        if (!is_bool($value)) {
            throw new UnexpectedValueException($message === null ? 'Expected a boolean' : $message);
        }
    }

    /**
     * Ensure that the value is an integer.
     *
     * @param int         $value   the value to validate
     * @param null|string $message an optional validation error message
     * @psalm-param mixed $value
     *
     * @throws UnexpectedValueException if the value does not satisfy the constraint
     *
     * @return void
     * @psalm-assert int $value
     */
    protected static function ensureInt($value, $message = null)
    {
        if (!is_int($value)) {
            throw new UnexpectedValueException($message === null ? 'Expected an integer' : $message);
        }
    }

    /**
     * Ensure that the value is an array.
     *
     * @param array<array-key, mixed> $value   the value to validate
     * @param null|string             $message an optional validation error message
     * @psalm-param mixed $value
     *
     * @throws UnexpectedValueException if the value does not satisfy the constraint
     *
     * @return void
     * @psalm-assert array<array-key, mixed> $value
     */
    protected static function ensureArray($value, $message = null)
    {
        if (!is_array($value)) {
            throw new UnexpectedValueException($message === null ? 'Expected an array' : $message);
        }
    }

    /**
     * Ensure that the value is an object.
     *
     * @param object      $value   the value to validate
     * @param null|string $message an optional validation error message
     * @psalm-param mixed $value
     *
     * @throws UnexpectedValueException if the value does not satisfy the constraint
     *
     * @return void
     * @psalm-assert object $value
     */
    protected static function ensureObject($value, $message = null)
    {
        if (!is_object($value)) {
            throw new UnexpectedValueException($message === null ? 'Expected an object' : $message);
        }
    }

    /**
     * Ensure that the value contains only decimal digits.
     *
     * @param numeric-string $value   the value to validate
     * @param null|string    $message an optional validation error message
     * @psalm-param mixed $value
     *
     * @throws UnexpectedValueException if the value does not satisfy the constraint
     *
     * @return void
     * @psalm-assert numeric-string $value
     */
    protected static function ensureDigitString($value, $message = null)
    {
        if (!is_string($value) || !is_numeric($value) || strspn($value, '0123456789') !== strlen($value)) {
            throw new UnexpectedValueException($message === null ? 'Expected a string of decimal digits' : $message);
        }
    }

    /**
     * Ensure that the value is a finite, non-negative number.
     *
     * @param float|int   $value   the value to validate
     * @param null|string $message an optional validation error message
     * @psalm-param mixed $value
     *
     * @throws UnexpectedValueException if the value does not satisfy the constraint
     *
     * @return void
     * @psalm-assert float|int $value
     */
    protected static function ensureNonNegativeNumber($value, $message = null)
    {
        if ((!is_int($value) && !is_float($value)) || !is_finite((float) $value) || $value < 0) {
            throw new UnexpectedValueException($message === null ? 'Expected a finite, non-negative number' : $message);
        }
    }

    /**
     * Ensure that the value is a non-empty string.
     *
     * @param non-empty-string $value   the value to validate
     * @param null|string      $message an optional validation error message
     * @psalm-param mixed $value
     *
     * @throws UnexpectedValueException if the value does not satisfy the constraint
     *
     * @return void
     * @psalm-assert non-empty-string $value
     */
    protected static function ensureNonEmptyString($value, $message = null)
    {
        self::ensureString($value, $message);

        if ($value === '') {
            throw new UnexpectedValueException($message === null ? 'Expected a non-empty string' : $message);
        }
    }

    /**
     * Ensure that the value is null or string.
     *
     * @param null|string $value   the value to validate
     * @param null|string $message an optional validation error message
     * @psalm-param mixed $value
     *
     * @throws UnexpectedValueException if the value does not satisfy the constraint
     *
     * @return void
     * @psalm-assert null|string $value
     */
    protected static function ensureNullableString($value, $message = null)
    {
        if ($value !== null) {
            self::ensureString($value, $message);
        }
    }

    /**
     * Ensure that the value is null or bool.
     *
     * @param null|bool   $value   the value to validate
     * @param null|string $message an optional validation error message
     * @psalm-param mixed $value
     *
     * @throws UnexpectedValueException if the value does not satisfy the constraint
     *
     * @return void
     * @psalm-assert null|bool $value
     */
    protected static function ensureNullableBool($value, $message = null)
    {
        if ($value !== null) {
            self::ensureBool($value, $message);
        }
    }

    /**
     * Ensure that the value is null or array<array-key, mixed>.
     *
     * @param null|array<array-key, mixed> $value   the value to validate
     * @param null|string                  $message an optional validation error message
     * @psalm-param mixed $value
     *
     * @throws UnexpectedValueException if the value does not satisfy the constraint
     *
     * @return void
     * @psalm-assert null|array<array-key, mixed> $value
     */
    protected static function ensureNullableArray($value, $message = null)
    {
        if ($value !== null) {
            self::ensureArray($value, $message);
        }
    }

    /**
     * Ensure that the value is null or float|int.
     *
     * @param null|float|int $value   the value to validate
     * @param null|string    $message an optional validation error message
     * @psalm-param mixed $value
     *
     * @throws UnexpectedValueException if the value does not satisfy the constraint
     *
     * @return void
     * @psalm-assert null|float|int $value
     */
    protected static function ensureNullableNonNegativeNumber($value, $message = null)
    {
        if ($value !== null) {
            self::ensureNonNegativeNumber($value, $message);
        }
    }

    /**
     * Ensure that the value is null or non-empty-string.
     *
     * @param null|non-empty-string $value   the value to validate
     * @param null|string           $message an optional validation error message
     * @psalm-param mixed $value
     *
     * @throws UnexpectedValueException if the value does not satisfy the constraint
     *
     * @return void
     * @psalm-assert null|non-empty-string $value
     */
    protected static function ensureNullableNonEmptyString($value, $message = null)
    {
        if ($value !== null) {
            self::ensureNonEmptyString($value, $message);
        }
    }

    /**
     * Ensure that the value is an integer or a string of decimal digits.
     *
     * @param int|numeric-string $value   the value to validate
     * @param null|string        $message an optional validation error message
     * @psalm-param mixed $value
     *
     * @throws UnexpectedValueException if the value does not satisfy the constraint
     *
     * @return void
     * @psalm-assert int|numeric-string $value
     */
    protected static function ensureIntOrDigitString($value, $message = null)
    {
        if (!is_int($value)) {
            self::ensureDigitString($value, $message);
        }
    }

    /**
     * Ensure that the value is within the inclusive integer bounds.
     *
     * @param int         $value   the value to validate
     * @param int         $minimum the inclusive lower bound
     * @param null|int    $maximum the inclusive upper bound, or null for no upper bound
     * @param null|string $message an optional validation error message
     * @psalm-param mixed $value
     *
     * @throws UnexpectedValueException if the value does not satisfy the constraint
     *
     * @return void
     * @psalm-assert int $value
     */
    protected static function ensureIntRange($value, $minimum, $maximum = null, $message = null)
    {
        self::ensureInt($value, $message);

        if ($value < $minimum || ($maximum !== null && $value > $maximum)) {
            throw new UnexpectedValueException($message === null ? 'Value is outside the allowed range' : $message);
        }
    }

    /**
     * Ensure that the value is within the inclusive integer bounds.
     *
     * @param int|numeric-string $value   the value to validate
     * @param int                $minimum the inclusive lower bound
     * @param null|int           $maximum the inclusive upper bound, or null for no upper bound
     * @param null|string        $message an optional validation error message
     * @psalm-param mixed $value
     *
     * @throws UnexpectedValueException if the value does not satisfy the constraint
     *
     * @return void
     * @psalm-assert int|numeric-string $value
     */
    protected static function ensureIntOrDigitStringRange($value, $minimum, $maximum = null, $message = null)
    {
        self::ensureIntOrDigitString($value, $message);

        if ($value < $minimum || ($maximum !== null && $value > $maximum)) {
            throw new UnexpectedValueException($message === null ? 'Value is outside the allowed range' : $message);
        }
    }

    /**
     * Ensure that the value is null or within the inclusive integer bounds.
     *
     * @param null|int    $value   the value to validate
     * @param int         $minimum the inclusive lower bound
     * @param null|int    $maximum the inclusive upper bound, or null for no upper bound
     * @param null|string $message an optional validation error message
     * @psalm-param mixed $value
     *
     * @throws UnexpectedValueException if the value does not satisfy the constraint
     *
     * @return void
     * @psalm-assert null|int $value
     */
    protected static function ensureNullableIntRange($value, $minimum, $maximum = null, $message = null)
    {
        if ($value !== null) {
            self::ensureIntRange($value, $minimum, $maximum, $message);
        }
    }

    /**
     * Ensure that the value is a string or an array.
     *
     * @param string|array<array-key, mixed> $value   the value to validate
     * @param null|string                    $message an optional validation error message
     * @psalm-param mixed $value
     *
     * @throws UnexpectedValueException if the value does not satisfy the constraint
     *
     * @return void
     * @psalm-assert string|array<array-key, mixed> $value
     */
    protected static function ensureStringOrArray($value, $message = null)
    {
        if (!is_string($value)) {
            self::ensureArray($value, $message);
        }
    }

    /**
     * Ensure that every array value is a string, without changing its keys.
     *
     * @param array<array-key, string> $value   the value to validate
     * @param null|string              $message an optional validation error message
     * @psalm-param mixed $value
     *
     * @throws UnexpectedValueException if the value does not satisfy the constraint
     *
     * @return void
     * @psalm-assert array<array-key, string> $value
     */
    protected static function ensureStringArray($value, $message = null)
    {
        self::ensureArray($value, $message);

        foreach ($value as $item) {
            self::ensureString($item, $message);
        }
    }

    /**
     * Ensure that the value is null or an array of strings.
     *
     * @param null|array<array-key, string> $value   the value to validate
     * @param null|string                   $message an optional validation error message
     * @psalm-param mixed $value
     *
     * @throws UnexpectedValueException if the value does not satisfy the constraint
     *
     * @return void
     * @psalm-assert null|array<array-key, string> $value
     */
    protected static function ensureNullableStringArray($value, $message = null)
    {
        if ($value !== null) {
            self::ensureStringArray($value, $message);
        }
    }

    /**
     * Ensure that the value is null, a string, or an array of strings.
     *
     * @param null|string|array<array-key, string> $value   the value to validate
     * @param null|string                          $message an optional validation error message
     * @psalm-param mixed $value
     *
     * @throws UnexpectedValueException if the value does not satisfy the constraint
     *
     * @return void
     * @psalm-assert null|string|array<array-key, string> $value
     */
    protected static function ensureNullableStringOrStringArray($value, $message = null)
    {
        if ($value !== null && !is_string($value)) {
            self::ensureStringArray($value, $message);
        }
    }

    /**
     * Ensure that the value is null, a boolean, or a string.
     *
     * @param null|bool|string $value   the value to validate
     * @param null|string      $message an optional validation error message
     * @psalm-param mixed $value
     *
     * @throws UnexpectedValueException if the value does not satisfy the constraint
     *
     * @return void
     * @psalm-assert null|bool|string $value
     */
    protected static function ensureNullableBoolOrString($value, $message = null)
    {
        if ($value !== null && !is_bool($value) && !is_string($value)) {
            throw new UnexpectedValueException($message === null ? 'Expected a boolean, string or null' : $message);
        }
    }

    /**
     * Ensure that every array key is a string.
     *
     * @param array<string, mixed> $value   the value to validate
     * @param null|string          $message an optional validation error message
     * @psalm-param mixed $value
     *
     * @throws UnexpectedValueException if the value does not satisfy the constraint
     *
     * @return void
     * @psalm-assert array<string, mixed> $value
     */
    protected static function ensureStringKeyedArray($value, $message = null)
    {
        self::ensureArray($value, $message);

        foreach (array_keys($value) as $key) {
            self::ensureString($key, $message);
        }
    }

    /**
     * Ensure that every array key and value is a string.
     *
     * @param array<string, string> $value   the value to validate
     * @param null|string           $message an optional validation error message
     * @psalm-param mixed $value
     *
     * @throws UnexpectedValueException if the value does not satisfy the constraint
     *
     * @return void
     * @psalm-assert array<string, string> $value
     */
    protected static function ensureStringMap($value, $message = null)
    {
        self::ensureStringKeyedArray($value, $message);
        self::ensureStringArray($value, $message);
    }

    /**
     * Ensure that the value is null or an array with string keys and values.
     *
     * @param null|array<string, string> $value   the value to validate
     * @param null|string                $message an optional validation error message
     * @psalm-param mixed $value
     *
     * @throws UnexpectedValueException if the value does not satisfy the constraint
     *
     * @return void
     * @psalm-assert null|array<string, string> $value
     */
    protected static function ensureNullableStringMap($value, $message = null)
    {
        if ($value !== null) {
            self::ensureStringMap($value, $message);
        }
    }

    /**
     * Ensure that the value is a non-empty array.
     *
     * @param non-empty-array<array-key, mixed> $value   the value to validate
     * @param null|string                       $message an optional validation error message
     * @psalm-param mixed $value
     *
     * @throws UnexpectedValueException if the value does not satisfy the constraint
     *
     * @return void
     * @psalm-assert !empty $value
     */
    protected static function ensureNonEmptyArray($value, $message = null)
    {
        self::ensureArray($value, $message);

        if ($value === []) {
            throw new UnexpectedValueException($message === null ? 'Expected a non-empty array' : $message);
        }
    }

    /**
     * Ensure that the array contains the specified key.
     *
     * @param array<array-key, mixed> $value   the value to validate
     * @param array-key               $key     the required array key
     * @param null|string             $message an optional validation error message
     * @psalm-param mixed $value
     *
     * @throws UnexpectedValueException if the value does not satisfy the constraint
     *
     * @return void
     */
    protected static function ensureArrayHasKey($value, $key, $message = null)
    {
        self::ensureArray($value, $message);

        if (!array_key_exists($key, $value)) {
            throw new UnexpectedValueException($message === null ? 'Expected the array to contain the required key' : $message);
        }
    }

    /**
     * Ensure that the value names an existing class or interface.
     *
     * @param class-string $value   the value to validate
     * @param null|string  $message an optional validation error message
     * @psalm-param mixed $value
     *
     * @throws UnexpectedValueException if the value does not satisfy the constraint
     *
     * @return void
     * @psalm-assert class-string $value
     */
    protected static function ensureClassString($value, $message = null)
    {
        self::ensureString($value, $message);

        if (!class_exists($value) && !interface_exists($value)) {
            throw new UnexpectedValueException($message === null ? 'Expected an existing class or interface name' : $message);
        }
    }

    /**
     * Ensure that the value is an instance of the specified class.
     *
     * @template T of object
     * @param T               $value   the value to validate
     * @param class-string<T> $class   the expected class or interface
     * @param null|string     $message an optional validation error message
     * @psalm-param mixed $value
     *
     * @throws UnexpectedValueException if the value does not satisfy the constraint
     *
     * @return void
     * @psalm-assert T $value
     */
    protected static function ensureInstanceOf($value, $class, $message = null)
    {
        if (!$value instanceof $class) {
            throw new UnexpectedValueException($message === null ? 'Expected an instance of '.$class : $message);
        }
    }

    /**
     * Ensure that the value is null or an instance of the specified class.
     *
     * @template T of object
     * @param null|T          $value   the value to validate
     * @param class-string<T> $class   the expected class or interface
     * @param null|string     $message an optional validation error message
     * @psalm-param mixed $value
     *
     * @throws UnexpectedValueException if the value does not satisfy the constraint
     *
     * @return void
     * @psalm-assert null|T $value
     */
    protected static function ensureNullableInstanceOf($value, $class, $message = null)
    {
        if ($value !== null) {
            self::ensureInstanceOf($value, $class, $message);
        }
    }

    /**
     * Ensure that the value strictly matches one of the allowed values.
     *
     * @param string                   $value   the value to validate
     * @param array<array-key, string> $values  the allowed values
     * @param null|string              $message an optional validation error message
     * @psalm-param mixed $value
     *
     * @throws InvalidFormatException if the value does not satisfy the constraint
     *
     * @return void
     * @psalm-assert =string $value
     */
    protected static function ensureOneOf($value, $values, $message = null)
    {
        if (!in_array($value, $values, true)) {
            throw new InvalidFormatException($value, $message === null ? $values : $message);
        }
    }

    /**
     * Ensure that the value is an open stream resource.
     *
     * @param resource    $value   the value to validate
     * @param null|string $message an optional validation error message
     * @psalm-param mixed $value
     *
     * @throws UnexpectedValueException if the value does not satisfy the constraint
     *
     * @return void
     * @psalm-assert resource $value
     */
    protected static function ensureStream($value, $message = null)
    {
        if (!is_resource($value) || get_resource_type($value) !== 'stream') {
            throw new UnexpectedValueException($message === null ? 'Expected an open stream resource' : $message);
        }
    }

    /**
     * Ensure that the value is null or an open stream resource.
     *
     * @param null|resource $value   the value to validate
     * @param null|string   $message an optional validation error message
     * @psalm-param mixed $value
     *
     * @throws UnexpectedValueException if the value does not satisfy the constraint
     *
     * @return void
     * @psalm-assert null|resource $value
     */
    protected static function ensureNullableStream($value, $message = null)
    {
        if ($value !== null) {
            self::ensureStream($value, $message);
        }
    }

    /**
     * Ensure that input is null, a scalar, an open stream, or traversable.
     *
     * @param null|scalar|resource|Traversable $value   the value to validate
     * @param null|string                      $message an optional validation error message
     * @psalm-param mixed $value
     *
     * @throws UnexpectedValueException if the value does not satisfy the constraint
     *
     * @return void
     * @psalm-assert null|scalar|resource|Traversable $value
     */
    protected static function ensureNullableScalarOrStreamOrTraversable($value, $message = null)
    {
        if ($value !== null && !is_scalar($value) && !$value instanceof Traversable) {
            self::ensureStream($value, $message);
        }
    }

    /**
     * Ensure that the value is a non-empty string or an instance of the given class.
     *
     * @template T of object
     * @param non-empty-string|T $value   the value to validate
     * @param class-string<T>    $class   the allowed class or interface
     * @param null|string        $message an optional validation error message
     * @psalm-param mixed $value
     *
     * @throws UnexpectedValueException if the value does not satisfy the constraint
     *
     * @return void
     * @psalm-assert non-empty-string|T $value
     */
    protected static function ensureNonEmptyStringOrInstanceOf($value, $class, $message = null)
    {
        if (is_string($value)) {
            self::ensureNonEmptyString($value, $message);
        } else {
            self::ensureInstanceOf($value, $class, $message);
        }
    }

    /**
     * Ensure that the value is a string, an array, or an instance of the given class.
     *
     * @template T of object
     * @param string|array<array-key, mixed>|T $value   the value to validate
     * @param class-string<T>                  $class   the allowed class or interface
     * @param null|string                      $message an optional validation error message
     * @psalm-param mixed $value
     *
     * @throws UnexpectedValueException if the value does not satisfy the constraint
     *
     * @return void
     * @psalm-assert string|array<array-key, mixed>|T $value
     */
    protected static function ensureStringOrArrayOrInstanceOf($value, $class, $message = null)
    {
        if (!is_string($value) && !is_array($value)) {
            self::ensureInstanceOf($value, $class, $message);
        }
    }

    /**
     * Ensure that a string of decimal digits is within the inclusive bounds.
     *
     * @param numeric-string $value   the value to validate
     * @param int            $minimum the inclusive lower bound
     * @param null|int       $maximum the inclusive upper bound, or null for no upper bound
     * @param null|string    $message an optional validation error message
     * @psalm-param mixed $value
     *
     * @throws UnexpectedValueException if the value does not satisfy the constraint
     *
     * @return void
     * @psalm-assert numeric-string $value
     */
    protected static function ensureDigitStringRange($value, $minimum, $maximum = null, $message = null)
    {
        self::ensureDigitString($value, $message);

        if ($value < $minimum || ($maximum !== null && $value > $maximum)) {
            throw new UnexpectedValueException($message === null ? 'Value is outside the allowed range' : $message);
        }
    }

    /**
     * Ensure that no value was supplied.
     *
     * @param null        $value   the value to validate
     * @param null|string $message an optional validation error message
     * @psalm-param mixed $value
     *
     * @throws UnexpectedValueException if the value does not satisfy the constraint
     *
     * @return void
     * @psalm-assert null $value
     */
    protected static function ensureNull($value, $message = null)
    {
        if ($value !== null) {
            throw new UnexpectedValueException($message === null ? 'Expected null' : $message);
        }
    }

    /**
     * Ensure that a string matches the expected format.
     *
     * @param string           $value   the value to validate
     * @param non-empty-string $pattern the regular expression describing the format
     * @param null|string      $message an optional validation error message
     * @psalm-param mixed $value
     *
     * @throws InvalidFormatException if the value does not satisfy the constraint
     *
     * @return void
     * @psalm-assert string $value
     */
    protected static function ensureMatches($value, $pattern, $message = null)
    {
        self::ensureString($value, $message);

        if (preg_match($pattern, $value) !== 1) {
            throw new InvalidFormatException($value, $message === null ? $pattern : $message);
        }
    }

    /**
     * Ensure that a scalar or object can be rendered as a string.
     *
     * @param scalar|NativeStringable $value   the value to validate
     * @param null|string             $message an optional validation error message
     * @psalm-param mixed $value
     *
     * @throws UnexpectedValueException if the value cannot be rendered
     *
     * @return void
     * @psalm-assert scalar|NativeStringable $value
     */
    protected static function ensureScalarOrStringable($value, $message = null)
    {
        if (!is_scalar($value) && (!is_object($value) || !method_exists($value, '__toString'))) {
            throw new UnexpectedValueException($message === null ? 'Expected a scalar or stringable object' : $message);
        }
    }

    /**
     * Ensure that a value is null, stringable, or an array of stringable values.
     *
     * @param null|scalar|NativeStringable|array<array-key, scalar|NativeStringable> $value   the value to validate
     * @param null|string                                                            $message an optional validation error message
     * @psalm-param mixed $value
     *
     * @throws UnexpectedValueException if a value cannot be rendered
     *
     * @return void
     * @psalm-assert null|scalar|NativeStringable|array<array-key, scalar|NativeStringable> $value
     */
    protected static function ensureNullableScalarOrStringableOrArray($value, $message = null)
    {
        if (is_array($value)) {
            foreach ($value as $item) {
                self::ensureScalarOrStringable($item, $message);
            }
        } elseif ($value !== null) {
            self::ensureScalarOrStringable($value, $message);
        }
    }

    /**
     * Ensure that an argument array is empty.
     *
     * @param array{}     $value   the value to validate
     * @param null|string $message an optional validation error message
     * @psalm-param mixed $value
     *
     * @throws UnexpectedValueException if any value was supplied
     *
     * @return void
     * @psalm-assert array{} $value
     */
    protected static function ensureEmptyArray($value, $message = null)
    {
        if ($value !== []) {
            throw new UnexpectedValueException($message === null ? 'Expected an empty array' : $message);
        }
    }

    /**
     * Ensure that a class name identifies an implementation of the expected type.
     *
     * @template T of object
     * @param class-string<T> $value   the class name to validate
     * @param class-string<T> $class   the required parent class or interface
     * @param null|string     $message an optional validation error message
     * @psalm-param mixed $value
     *
     * @throws UnexpectedValueException if the name is not a matching class
     *
     * @return void
     * @psalm-assert class-string<T> $value
     */
    protected static function ensureClassStringOf($value, $class, $message = null)
    {
        self::ensureString($value, $message);

        if (!class_exists($value) || !is_a($value, $class, true)) {
            throw new UnexpectedValueException($message === null ? 'Expected a class implementing '.$class : $message);
        }
    }

    /**
     * Ensure that a value identifies or implements the expected object type.
     *
     * @template T of object
     * @param class-string<T>|T $value   the class name or instance to validate
     * @param class-string<T>   $class   the required parent class or interface
     * @param null|string       $message an optional validation error message
     * @psalm-param mixed $value
     *
     * @throws UnexpectedValueException if the value does not match the required type
     *
     * @return void
     * @psalm-assert class-string<T>|T $value
     */
    protected static function ensureClassStringOrInstanceOf($value, $class, $message = null)
    {
        if (is_string($value)) {
            self::ensureClassStringOf($value, $class, $message);
        } else {
            self::ensureInstanceOf($value, $class, $message);
        }
    }
}
