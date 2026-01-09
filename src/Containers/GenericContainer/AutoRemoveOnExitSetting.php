<?php

namespace Testcontainers\Containers\GenericContainer;

/**
 * AutoRemoveOnExitSetting is a trait that provides the ability to automatically remove a container when it exits.
 *
 * Two formats are supported:
 * 1. static variable `$AUTO_REMOVE_ON_EXIT`:
 *
 * <code>
 * class YourContainer extends GenericContainer
 * {
 *     protected static $AUTO_REMOVE_ON_EXIT = true;
 * }
 * </code>
 *
 * 2. method `withAutoRemoveOnExit`:
 *
 * <code>
 * $container = (new YourContainer('image'))
 *     ->withAutoRemoveOnExit(true);
 * </code>
 */
trait AutoRemoveOnExitSetting
{
    /**
     * Define the default auto-remove on exit mode to be used for the container.
     *
     * @var null|bool
     */
    protected static $AUTO_REMOVE_ON_EXIT;

    /**
     * Whether to automatically remove the container when it exits.
     *
     * @var bool
     */
    private $autoRemoveOnExit = false;

    /**
     * Set whether to automatically remove the container when it exits.
     * Similar to the `--rm` option on the Docker CLI.
     *
     * @param bool $autoRemoveOnExit whether to automatically remove the container on exit
     *
     * @return self
     */
    public function withAutoRemoveOnExit($autoRemoveOnExit)
    {
        $this->autoRemoveOnExit = $autoRemoveOnExit;

        return $this;
    }

    /**
     * Retrieve whether to automatically remove the container when it exits.
     *
     * If the static property is set to true, it takes precedence. Otherwise,
     * the instance property set via withAutoRemoveOnExit() is used.
     *
     * @return bool true if the container should be automatically removed on exit, false otherwise
     */
    protected function autoRemoveOnExit()
    {
        if (static::$AUTO_REMOVE_ON_EXIT) {
            return static::$AUTO_REMOVE_ON_EXIT;
        }

        return $this->autoRemoveOnExit;
    }
}
