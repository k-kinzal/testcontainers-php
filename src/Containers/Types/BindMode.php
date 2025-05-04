<?php

namespace Testcontainers\Containers\Types;

use Testcontainers\Exceptions\InvalidFormatException;
use Testcontainers\Utility\Stringable;

class BindMode implements Stringable
{
    /**
     * The mode for read-only bind.
     *
     * @var string
     */
    public static $READ_ONLY = 'ro';

    /**
     * The mode for read-write bind.
     *
     * @var string
     */
    public static $READ_WRITE = 'rw';

    /**
     * The mode of the bind (e.g., read-only or read-write).
     *
     * @var string
     */
    private $mode;

    /**
     * @param string $mode
     */
    public function __construct($mode)
    {
        assert(in_array($mode, [static::$READ_ONLY, static::$READ_WRITE]));

        $this->mode = $mode;
    }

    public function __toString()
    {
        return $this->toString();
    }

    /**
     * Create a new instance of `BindMode` with the `READ_ONLY` mode.
     *
     * @return self
     */
    public static function READ_ONLY()
    {
        return new self(static::$READ_ONLY);
    }

    /**
     * Create a new instance of `BindMode` with the `READ_WRITE` mode.
     *
     * @return self
     */
    public static function READ_WRITE()
    {
        return new self(static::$READ_WRITE);
    }

    /**
     * Create a new instance of `BindMode` from a string representation.
     *
     * @param string $mode the string representation of the bind mode
     *
     * @throws InvalidFormatException if the provided mode is not valid
     *
     * @return self
     */
    public static function fromString($mode)
    {
        if (!in_array($mode, [static::$READ_ONLY, static::$READ_WRITE])) {
            throw new InvalidFormatException($mode, [static::$READ_ONLY, static::$READ_WRITE]);
        }

        return new self($mode);
    }

    /**
     * Check if the bind mode is set to read-only.
     *
     * @return bool true if the mode is read-only, false otherwise
     */
    public function isReadOnly()
    {
        return $this->mode === static::$READ_ONLY;
    }

    /**
     * Check if the bind mode is set to read-write.
     *
     * @return bool true if the mode is read-write, false otherwise
     */
    public function isReadWrite()
    {
        return $this->mode === static::$READ_WRITE;
    }

    /**
     * Get the string representation of the bind mode.
     *
     * @return string the string representation of the bind mode
     */
    public function toString()
    {
        return $this->mode;
    }
}
