<?php

namespace Testcontainers\Containers\GenericContainer;

/**
 * InspectIntervalSetting is a trait that provides the ability to set the inspect interval for a container.
 *
 * Two formats are supported:
 * 1. static variable `$INSPECT_INTERVAL`:
 *
 * <code>
 * class YourContainer extends GenericContainer
 * {
 *     protected static $INSPECT_INTERVAL = 1;
 * }
 * </code>
 *
 * 2. method `withInspectInterval`:
 *
 * <code>
 * $container = (new YourContainer('image'))
 *     ->withInspectInterval(1);
 * </code>
 */
trait InspectIntervalSetting
{
    /**
     * Define the default inspect interval to be used for the container.
     *
     * @var null|float|int
     * @psalm-var mixed
     */
    protected static $INSPECT_INTERVAL;

    /**
     * The inspect interval in seconds.
     *
     * @var null|float|int
     */
    private $inspectInterval;

    /**
     * Set the interval in seconds for inspecting the container.
     * A value of 0 will inspect the container every time its state is checked.
     *
     * @param float|int $seconds the inspect interval in seconds
     * @psalm-param mixed $seconds
     *
     * @return self
     */
    public function withInspectInterval($seconds)
    {
        self::ensureNonNegativeNumber($seconds);

        $this->inspectInterval = $seconds;

        return $this;
    }

    /**
     * Retrieve the inspect interval for the container.
     *
     * If the static property is set (non-null), it takes precedence. Otherwise,
     * the instance property set via withInspectInterval() is used.
     *
     * @return null|float|int the inspect interval in seconds, or null if not set (10 seconds will be used)
     */
    protected function inspectInterval()
    {
        if (static::$INSPECT_INTERVAL !== null) {
            self::ensureNonNegativeNumber(static::$INSPECT_INTERVAL);

            return static::$INSPECT_INTERVAL;
        }

        return $this->inspectInterval;
    }
}
