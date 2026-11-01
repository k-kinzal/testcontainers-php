<?php

namespace Testcontainers\Containers;

use Testcontainers\Utility\Ensure;
use Testcontainers\Exceptions\InvalidFormatException;
use Testcontainers\Utility\Stringable;

/**
 * Represents the mode for container reuse behavior.
 *
 * This class defines how containers should behave when the same container class
 * is run multiple times:
 * - ADD: Always start a new container (default behavior)
 * - RESTART: Stop existing container and start a new one
 * - REUSE: Reuse existing container if it's still running
 */
class ReuseMode implements Stringable
{
    use Ensure;

    /**
     * Add mode: always start a new container.
     *
     * @var string
     * @psalm-var mixed
     */
    public static $ADD = 'add';

    /**
     * Restart mode: stop existing container and start a new one.
     *
     * @var string
     * @psalm-var mixed
     */
    public static $RESTART = 'restart';

    /**
     * Reuse mode: reuse existing container if running.
     *
     * @var string
     * @psalm-var mixed
     */
    public static $REUSE = 'reuse';

    /**
     * The reuse mode value.
     *
     * @var string
     */
    private $mode;

    /**
     * @param string $mode
     * @psalm-param mixed $mode
     */
    public function __construct($mode)
    {
        self::ensureString($mode);

        $values = self::ensureStringList([static::$ADD, static::$RESTART, static::$REUSE]);

        if (!in_array($mode, $values, true)) {
            throw new InvalidFormatException($mode, $values);
        }

        $this->mode = $mode;
    }

    public function __toString()
    {
        return $this->toString();
    }

    /**
     * Creates a ReuseMode instance with the add mode.
     *
     * @return self
     */
    public static function ADD()
    {
        return new self(static::$ADD);
    }

    /**
     * Creates a ReuseMode instance with the restart mode.
     *
     * @return self
     */
    public static function RESTART()
    {
        return new self(static::$RESTART);
    }

    /**
     * Creates a ReuseMode instance with the reuse mode.
     *
     * @return self
     */
    public static function REUSE()
    {
        return new self(static::$REUSE);
    }

    /**
     * Creates a ReuseMode instance from a string.
     *
     * @param string $mode The reuse mode. Valid values are 'add', 'restart', or 'reuse'.
     * @psalm-param mixed $mode
     *
     * @throws InvalidFormatException if the mode is invalid
     *
     * @return ReuseMode
     */
    public static function fromString($mode)
    {
        self::ensureString($mode);

        $values = self::ensureStringList([static::$ADD, static::$RESTART, static::$REUSE]);

        if (!in_array($mode, $values, true)) {
            throw new InvalidFormatException($mode, $values);
        }

        return new self($mode);
    }

    /**
     * Checks if the mode is add.
     *
     * @return bool
     */
    public function isAdd()
    {
        return $this->mode === static::$ADD;
    }

    /**
     * Checks if the mode is restart.
     *
     * @return bool
     */
    public function isRestart()
    {
        return $this->mode === static::$RESTART;
    }

    /**
     * Checks if the mode is reuse.
     *
     * @return bool
     */
    public function isReuse()
    {
        return $this->mode === static::$REUSE;
    }

    /**
     * Converts the reuse mode to a string representation.
     *
     * @return string
     */
    public function toString()
    {
        return $this->mode;
    }
}
