<?php

namespace Testcontainers\Containers\GenericContainer;

use function Testcontainers\ensure;

/**
 * StopSignalSetting is a trait that provides the ability to set the stop signal for a container.
 *
 * Two formats are supported:
 * 1. static variable `$STOP_SIGNAL`:
 *
 * <code>
 * class YourContainer extends GenericContainer
 * {
 *     protected static $STOP_SIGNAL = 'KILL';
 * }
 * </code>
 *
 * 2. method `withStopSignal`:
 *
 * <code>
 * $container = (new YourContainer('image'))
 *     ->withStopSignal('KILL');
 * </code>
 */
trait StopSignalSetting
{
    /**
     * Define the default stop signal to be used for the container.
     *
     * @var null|string
     */
    protected static $STOP_SIGNAL;

    /**
     * The stop signal name.
     *
     * @var null|string
     */
    private $stopSignal;

    /**
     * Set the signal to send when stopping the container.
     * Equivalent to the `--signal` option on the `docker stop` command.
     *
     * Common signals: 'KILL' (SIGKILL, immediate), 'TERM' (SIGTERM, graceful, default),
     * 'INT' (SIGINT), 'QUIT' (SIGQUIT), 'HUP' (SIGHUP).
     *
     * Note: The `--signal` option requires Docker 23.0+ (API 1.42+).
     *
     * @param string $signal the signal name (e.g. 'KILL', 'TERM', 'INT')
     *
     * @return self
     */
    public function withStopSignal($signal)
    {
        /** @psalm-suppress RedundantConditionGivenDocblockType */
        ensure(is_string($signal), '$signal must be string');

        $this->stopSignal = $signal;

        return $this;
    }

    /**
     * Retrieve the stop signal for the container.
     *
     * If the static property is set (non-null), it takes precedence. Otherwise,
     * the instance property set via withStopSignal() is used.
     *
     * @return null|string the stop signal name, or null if not set (Docker default SIGTERM will be used)
     */
    protected function stopSignal()
    {
        /** @psalm-suppress RedundantConditionGivenDocblockType */
        ensure(static::$STOP_SIGNAL === null || is_string(static::$STOP_SIGNAL), 'static::$STOP_SIGNAL must be null|string');

        if (static::$STOP_SIGNAL !== null) {
            return static::$STOP_SIGNAL;
        }

        return $this->stopSignal;
    }
}
