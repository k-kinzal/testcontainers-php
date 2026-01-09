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
     * This method returns whether the container should be automatically removed when it exits.
     * If a specific auto-remove on exit mode is set, it will return that. Otherwise, it will
     * attempt to retrieve the default auto-remove on exit mode from the provider.
     *
     * @return bool true if the container should be automatically removed on exit, false otherwise
     */
    protected function autoRemoveOnExit()
    {
        if (static::$AUTO_REMOVE_ON_EXIT !== null) {
            return static::$AUTO_REMOVE_ON_EXIT;
        }

        return $this->autoRemoveOnExit;
    }
}
