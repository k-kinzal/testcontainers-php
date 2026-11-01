<?php

namespace Testcontainers\Utility;

/**
 * Runtime checks for untrusted input. Do not use PHP assert(), which can be disabled.
 * Assertions refine the checked value; list helpers also return normalized values.
 */
trait Ensure
{
    /**
     * @param mixed $value
     * @param null|string $message
     * @return string
     * @psalm-assert string $value
     */
    protected static function ensureString($value, $message = null)
    {
        if (!is_string($value)) {
            throw new \InvalidArgumentException($message === null ? 'Expected a string' : $message);
        }

        return $value;
    }

    /**
     * @param mixed $value
     * @param null|string $message
     * @return non-empty-string
     * @psalm-assert non-empty-string $value
     */
    protected static function ensureNonEmptyString($value, $message = null)
    {
        self::ensureString($value, $message);

        if ($value === '') {
            throw new \InvalidArgumentException($message === null ? 'Expected a non-empty string' : $message);
        }

        return $value;
    }

    /**
     * @param mixed $value
     * @param null|string $message
     * @return null|string
     * @psalm-assert null|string $value
     */
    protected static function ensureNullableString($value, $message = null)
    {
        return $value === null ? null : self::ensureString($value, $message);
    }

    /**
     * @param mixed $value
     * @param null|string $message
     * @return bool
     * @psalm-assert bool $value
     */
    protected static function ensureBool($value, $message = null)
    {
        if (!is_bool($value)) {
            throw new \InvalidArgumentException($message === null ? 'Expected a boolean' : $message);
        }

        return $value;
    }

    /**
     * @param mixed $value
     * @param null|string $message
     * @return null|bool
     * @psalm-assert null|bool $value
     */
    protected static function ensureNullableBool($value, $message = null)
    {
        return $value === null ? null : self::ensureBool($value, $message);
    }

    /**
     * @param mixed $value
     * @param null|string $message
     * @return int
     * @psalm-assert int $value
     */
    protected static function ensureInt($value, $message = null)
    {
        if (!is_int($value)) {
            throw new \InvalidArgumentException($message === null ? 'Expected an integer' : $message);
        }

        return $value;
    }

    /**
     * @param mixed $value
     * @param int $minimum
     * @param null|int $maximum
     * @param null|string $message
     * @return int
     * @psalm-assert int $value
     */
    protected static function ensureIntRange($value, $minimum, $maximum = null, $message = null)
    {
        self::ensureInt($value, $message);

        if ($value < $minimum || ($maximum !== null && $value > $maximum)) {
            throw new \InvalidArgumentException($message === null ? 'Integer is outside the allowed range' : $message);
        }

        return $value;
    }

    /**
     * @param mixed $value
     * @param null|string $message
     * @return float|int
     * @psalm-assert float|int $value
     */
    protected static function ensureNonNegativeNumber($value, $message = null)
    {
        if ((!is_int($value) && !is_float($value)) || !is_finite((float) $value) || $value < 0) {
            throw new \InvalidArgumentException($message === null ? 'Expected a finite, non-negative number' : $message);
        }

        return $value;
    }

    /**
     * @param mixed $value
     * @param null|string $message
     * @return array<array-key, mixed>
     * @psalm-assert array<array-key, mixed> $value
     */
    protected static function ensureArray($value, $message = null)
    {
        if (!is_array($value)) {
            throw new \InvalidArgumentException($message === null ? 'Expected an array' : $message);
        }

        return $value;
    }

    /**
     * @param mixed $value
     * @param null|string $message
     * @return null|array<array-key, mixed>
     * @psalm-assert null|array<array-key, mixed> $value
     */
    protected static function ensureNullableArray($value, $message = null)
    {
        return $value === null ? null : self::ensureArray($value, $message);
    }

    /**
     * @param mixed $value
     * @param null|string $message
     * @return string|array<array-key, mixed>
     * @psalm-assert string|array<array-key, mixed> $value
     */
    protected static function ensureStringOrArray($value, $message = null)
    {
        return is_string($value) ? $value : self::ensureArray($value, $message);
    }

    /**
     * @param mixed $value
     * @param null|string $message
     * @return list<string>
     */
    protected static function ensureStringList($value, $message = null)
    {
        self::ensureArray($value, $message);

        $result = [];
        foreach ($value as $item) {
            $result[] = self::ensureString($item, $message);

        }

        return $result;
    }

    /**
     * @param mixed $value
     * @param null|string $message
     * @return null|list<string>
     * @psalm-assert null|array<array-key, string> $value
     */
    protected static function ensureNullableStringList($value, $message = null)
    {
        return $value === null ? null : self::ensureStringList($value, $message);
    }

    /**
     * @param mixed $value
     * @param null|string $message
     * @return null|string|list<string>
     * @psalm-assert null|string|array<array-key, string> $value
     */
    protected static function ensureNullableStringOrStringList($value, $message = null)
    {
        if ($value === null || is_string($value)) {
            return $value;
        }

        return self::ensureStringList($value, $message);
    }

    /**
     * @param mixed $value
     * @param null|string $message
     * @return null|bool|string
     * @psalm-assert null|bool|string $value
     */
    protected static function ensureNullableBoolOrString($value, $message = null)
    {
        if ($value !== null && !is_bool($value) && !is_string($value)) {
            throw new \InvalidArgumentException($message === null ? 'Expected a boolean, string or null' : $message);
        }

        return $value;
    }

    /**
     * @param mixed $value
     * @param null|string $message
     * @return array<string, string>
     * @psalm-assert array<string, string> $value
     */
    protected static function ensureStringMap($value, $message = null)
    {
        self::ensureArray($value, $message);

        $result = [];
        foreach ($value as $key => $item) {
            self::ensureString($key, $message);

            $result[$key] = self::ensureString($item, $message);

        }

        return $result;
    }

    /**
     * @param mixed $value
     * @param null|string $message
     * @return array<string, mixed>
     * @psalm-assert array<string, mixed> $value
     */
    protected static function ensureStringKeyedArray($value, $message = null)
    {
        self::ensureArray($value, $message);

        foreach (array_keys($value) as $key) {
            self::ensureString($key, $message);

        }

        // The key check above leaves the values deliberately untrusted.
        /** @var array<string, mixed> $value */
        return $value;
    }

    /**
     * @param mixed $value
     * @param null|string $message
     * @return class-string
     * @psalm-assert class-string $value
     */
    protected static function ensureClassString($value, $message = null)
    {
        self::ensureString($value, $message);

        if (!class_exists($value) && !interface_exists($value)) {
            throw new \InvalidArgumentException($message === null ? 'Expected an existing class or interface name' : $message);
        }

        return $value;
    }

    /**
     * @param mixed $value
     * @param null|string $message
     * @return int|numeric-string
     * @psalm-assert int|numeric-string $value
     */
    protected static function ensureIntOrDigitString($value, $message = null)
    {
        if (is_int($value)) {
            return $value;
        }

        return self::ensureDigitString($value, $message);
    }

    /**
     * @param mixed $value
     * @param null|string $message
     * @return numeric-string
     * @psalm-assert numeric-string $value
     */
    protected static function ensureDigitString($value, $message = null)
    {
        if (!is_string($value) || !is_numeric($value) || strspn($value, '0123456789') !== strlen($value)) {
            throw new \InvalidArgumentException($message === null ? 'Expected a string of decimal digits' : $message);
        }

        return $value;
    }

    /**
     * @template T of object
     * @param mixed $value
     * @param class-string<T> $class
     * @param null|string $message
     * @return T
     * @psalm-assert T $value
     */
    protected static function ensureInstanceOf($value, $class, $message = null)
    {
        if (!$value instanceof $class) {
            throw new \InvalidArgumentException($message === null ? 'Expected an instance of '.$class : $message);
        }

        return $value;
    }

    /**
     * @template T of object
     * @param mixed $value
     * @param class-string<T> $class
     * @param null|string $message
     * @return null|T
     * @psalm-assert null|T $value
     */
    protected static function ensureNullableInstanceOf($value, $class, $message = null)
    {
        return $value === null ? null : self::ensureInstanceOf($value, $class, $message);
    }
}
