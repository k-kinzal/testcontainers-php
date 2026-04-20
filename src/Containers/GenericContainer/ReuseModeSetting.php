<?php

namespace Testcontainers\Containers\GenericContainer;

use Testcontainers\Containers\ReuseMode;
use Testcontainers\Exceptions\InvalidFormatException;

use function Testcontainers\ensure;

/**
 * ReuseModeSetting is a trait that provides the ability to set the reuse mode for a container.
 *
 * Two formats are supported:
 * 1. static variable `$REUSE_MODE`:
 *
 * <code>
 * class YourContainer extends GenericContainer
 * {
 *     protected static $REUSE_MODE = ReuseMode::$REUSE;
 * }
 * </code>
 *
 * 2. method `withReuseMode`:
 *
 * <code>
 * $container = (new YourContainer('image'))
 *     ->withReuseMode(ReuseMode::REUSE());
 * </code>
 */
trait ReuseModeSetting
{
    /**
     * Define the default reuse mode for the container.
     *
     * @var null|string
     */
    protected static $REUSE_MODE;

    /**
     * The reuse mode for the container.
     *
     * @var null|ReuseMode
     */
    private $reuseMode;

    /**
     * Set the reuse mode for the container.
     *
     * @param ReuseMode $reuseMode
     *
     * @return self
     */
    public function withReuseMode($reuseMode)
    {
        ensure($reuseMode instanceof ReuseMode, '$reuseMode must be ReuseMode');

        $this->reuseMode = $reuseMode;

        return $this;
    }

    /**
     * Retrieve the reuse mode for the container.
     *
     * @return ReuseMode
     *
     * @throws InvalidFormatException if the reuse mode is not valid
     */
    public function reuseMode()
    {
        ensure(static::$REUSE_MODE === null || is_string(static::$REUSE_MODE), 'static::$REUSE_MODE must be null|string');

        if (static::$REUSE_MODE !== null) {
            return ReuseMode::fromString(static::$REUSE_MODE);
        }
        if ($this->reuseMode) {
            return $this->reuseMode;
        }

        return ReuseMode::ADD();
    }
}
