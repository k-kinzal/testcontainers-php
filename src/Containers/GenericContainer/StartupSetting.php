<?php

namespace Testcontainers\Containers\GenericContainer;

use LogicException;
use Testcontainers\Containers\StartupCheckStrategy\StartupCheckStrategy;
use Testcontainers\Containers\StartupCheckStrategy\StartupCheckStrategyProvider;

/**
 * StartupSetting is a trait that provides the ability to set the startup timeout and check strategy for a container.
 *
 * Two formats are supported:
 * 1. static variable `$STARTUP_TIMEOUT` and `$STARTUP_CHECK_STRATEGY`:
 *
 * <code>
 * class YourContainer extends GenericContainer
 * {
 *     protected static $STARTUP_TIMEOUT = 30;
 *
 *     protected static $STARTUP_CHECK_STRATEGY = 'is_running';
 * }
 * </code>
 *
 * 2. method `withStartupTimeout` and `withStartupCheckStrategy`:
 *
 * <code>
 * $container = (new YourContainer('image'))
 *     ->withStartupTimeout(30)
 *     ->withStartupCheckStrategy(new IsRunningStartupCheckStrategy());
 * </code>
 */
trait StartupSetting
{
    /**
     * Define the default startup timeout to be used for the container.
     * @var int|null
     */
    protected static $STARTUP_TIMEOUT;

    /**
     * The startup timeout to be used for the container.
     * @var int|null
     */
    private $startupTimeout;

    /**
     * Define the default startup check strategy to be used for the container.
     * @var string|null
     */
    protected static $STARTUP_CHECK_STRATEGY;

    /**
     * The startup check strategy to be used for the container.
     * @var StartupCheckStrategy|null
     */
    private $startupCheckStrategy;

    /**
     * The startup check strategy provider.
     * @var StartupCheckStrategyProvider
     */
    private $startupCheckStrategyProvider;

    /**
     * Set the duration of waiting time until the container is treated as started.
     *
     * @param int $timeout The duration to wait.
     * @return self
     */
    public function withStartupTimeout($timeout)
    {
        $this->startupTimeout = $timeout;

        return $this;
    }

    /**
     * Set the startup check strategy used for checking whether the container has started.
     *
     * @param StartupCheckStrategy $strategy The startup check strategy to use.
     * @return self
     */
    public function withStartupCheckStrategy($strategy)
    {
        $this->startupCheckStrategy = $strategy;

        return $this;
    }

    /**
     * Retrieve the startup timeout for the container.
     *
     * @return int|null
     */
    protected function startupTimeout()
    {
        if (static::$STARTUP_TIMEOUT) {
            return static::$STARTUP_TIMEOUT;
        }
        return $this->startupTimeout;
    }

    /**
     * Retrieve the startup check strategy for the container.
     *
     * This method returns the startup check strategy that should be used for the container.
     * If a specific startup check strategy is set, it will return that. Otherwise, it will
     * attempt to retrieve the default startup check strategy from the provider.
     *
     * @return StartupCheckStrategy|null The startup check strategy to be used, or null if none is set.
     */
    protected function startupCheckStrategy()
    {
        if (static::$STARTUP_CHECK_STRATEGY !== null) {
            $strategy = $this->startupCheckStrategyProvider->get(static::$STARTUP_CHECK_STRATEGY);
            if (!$strategy) {
                throw new LogicException("Startup check strategy not found: " . static::$STARTUP_CHECK_STRATEGY);
            }
            return $strategy;
        }
        if ($this->startupCheckStrategy) {
            return $this->startupCheckStrategy;
        }
        return null;
    }

    /**
     * Register a startup check strategy.
     *
     * @param StartupCheckStrategyProvider $provider The startup check strategy provider.
     */
    protected function registerStartupCheckStrategy($provider)
    {
        // Override this method to register custom startup strategies
    }
}
