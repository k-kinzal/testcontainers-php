<?php

namespace Testcontainers\Containers\GenericContainer;

use function Testcontainers\ensure;

/**
 * StopTimeoutSetting is a trait that provides the ability to set the stop timeout for a container.
 *
 * Two formats are supported:
 * 1. static variable `$STOP_TIMEOUT`:
 *
 * <code>
 * class YourContainer extends GenericContainer
 * {
 *     protected static $STOP_TIMEOUT = 0;
 * }
 * </code>
 *
 * 2. method `withStopTimeout`:
 *
 * <code>
 * $container = (new YourContainer('image'))
 *     ->withStopTimeout(0);
 * </code>
 */
trait StopTimeoutSetting
{
    /**
     * Define the default stop timeout to be used for the container.
     *
     * @var null|int
     */
    protected static $STOP_TIMEOUT;

    /**
     * The stop timeout in seconds.
     *
     * @var null|int
     */
    private $stopTimeout;

    /**
     * Set the stop timeout in seconds for the container.
     * Equivalent to the `--time` option on the `docker stop` command.
     * A value of 0 will send SIGTERM and immediately follow with SIGKILL.
     *
     * @param int $seconds the stop timeout in seconds
     *
     * @return self
     */
    public function withStopTimeout($seconds)
    {
        ensure(is_int($seconds), '$seconds must be int');

        $this->stopTimeout = $seconds;

        return $this;
    }

    /**
     * Retrieve the stop timeout for the container.
     *
     * If the static property is set (non-null), it takes precedence. Otherwise,
     * the instance property set via withStopTimeout() is used.
     *
     * @return null|int the stop timeout in seconds, or null if not set (Docker default will be used)
     */
    protected function stopTimeout()
    {
        ensure(static::$STOP_TIMEOUT === null || is_int(static::$STOP_TIMEOUT), 'static::$STOP_TIMEOUT must be null|int');

        if (static::$STOP_TIMEOUT !== null) {
            return static::$STOP_TIMEOUT;
        }

        return $this->stopTimeout;
    }
}
