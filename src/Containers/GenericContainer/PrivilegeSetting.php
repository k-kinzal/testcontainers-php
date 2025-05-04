<?php

namespace Testcontainers\Containers\GenericContainer;

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
        if (static::$PRIVILEGED) {
            return static::$PRIVILEGED;
        }

        return $this->privileged;
    }
}
