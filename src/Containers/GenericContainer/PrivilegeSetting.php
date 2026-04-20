<?php

namespace Testcontainers\Containers\GenericContainer;

use function Testcontainers\ensure;

/**
 * PrivilegeSetting is a trait that provides the ability to run a container in privileged mode.
 *
 * Two formats are supported:
 * 1. static variable `$PRIVILEGED`:
 *
 * <code>
 * class YourContainer extends GenericContainer
 * {
 *     protected static $PRIVILEGED = true;
 * }
 * </code>
 *
 * 2. method `withPrivilegedMode`:
 *
 * <code>
 * $container = (new YourContainer('image'))
 *     ->withPrivilegedMode(true);
 * </code>
 */
trait PrivilegeSetting
{
    /**
     * Define the default privileged mode to be used for the container.
     *
     * @var null|bool
     */
    protected static $PRIVILEGED;

    /**
     * The privileged mode to be used for the container.
     *
     * @var bool
     */
    private $privileged = false;

    /**
     * Set the privileged mode for the container.
     *
     * @param bool $mode whether to enable privileged mode
     *
     * @return self
     */
    public function withPrivilegedMode($mode)
    {
        /** @psalm-suppress RedundantConditionGivenDocblockType */
        ensure(is_bool($mode), '$mode must be bool');

        $this->privileged = $mode;

        return $this;
    }

    /**
     * Retrieve the privileged mode for the container.
     *
     * This method returns whether the container should run in privileged mode.
     * If a specific privileged mode is set, it will return that. Otherwise, it will
     * attempt to retrieve the default privileged mode from the provider.
     *
     * @return bool true if the container should run in privileged mode, false otherwise
     */
    protected function privileged()
    {
        /** @psalm-suppress RedundantConditionGivenDocblockType */
        ensure(static::$PRIVILEGED === null || is_bool(static::$PRIVILEGED), 'static::$PRIVILEGED must be null|bool');

        if (static::$PRIVILEGED !== null) {
            return static::$PRIVILEGED;
        }

        return $this->privileged;
    }
}
