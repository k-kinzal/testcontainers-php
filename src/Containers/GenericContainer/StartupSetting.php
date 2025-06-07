<?php

namespace Testcontainers\Containers\GenericContainer;

use Testcontainers\Containers\ContainerInstance;
use Testcontainers\Containers\StartupCheckStrategy\AlreadyExistsStartupStrategyException;
use Testcontainers\Containers\StartupCheckStrategy\IsRunningStartupCheckStrategy;
use Testcontainers\Containers\StartupCheckStrategy\StartupCheckStrategy;
use Testcontainers\Containers\StartupCheckStrategy\StartupCheckStrategyProvider;

/**
 * StartupSetting is a trait that provides the ability to set the startup timeout and check strategy for a container.
 *
 * Two formats are supported:
 * 1. static variable `$STARTUP_TIMEOUT`, `$STARTUP_CHECK_STRATEGY`, and `$STARTUP_CONFLICT_RETRY_ATTEMPTS`:
 *
 * <code>
 * class YourContainer extends GenericContainer
 * {
 *     protected static $STARTUP_TIMEOUT = 30;
 *
 *     protected static $STARTUP_CHECK_STRATEGY = 'is_running';
 *
 *     protected static $STARTUP_CONFLICT_RETRY_ATTEMPTS = 5;
 * }
 * </code>
 *
 * 2. method `withStartupTimeout`, `withStartupCheckStrategy`, and `withStartupConflictRetries`:
 *
 * <code>
 * $container = (new YourContainer('image'))
 *     ->withStartupTimeout(30)
 *     ->withStartupCheckStrategy(new IsRunningStartupCheckStrategy())
 *     ->withStartupConflictRetries(5);
 * </code>
 *
 * Default startup conflict retry attempts: 3
 */
trait StartupSetting
{
    /**
     * Default number of retry attempts for startup conflicts (port/bind address conflicts).
     */
    private static $DEFAULT_STARTUP_CONFLICT_RETRY_ATTEMPTS = 3;

    /**
     * Define the default startup timeout to be used for the container.
     *
     * @var null|int
     */
    protected static $STARTUP_TIMEOUT;

    /**
     * Define the default startup check strategy to be used for the container.
     *
     * @var null|string
     */
    protected static $STARTUP_CHECK_STRATEGY;

    /**
     * Define the default number of retry attempts for startup conflicts (port/bind address conflicts).
     *
     * @var null|int
     */
    protected static $STARTUP_CONFLICT_RETRY_ATTEMPTS;

    /**
     * The startup timeout to be used for the container.
     *
     * @var null|int
     */
    private $startupTimeout;

    /**
     * The startup check strategy to be used for the container.
     *
     * @var null|StartupCheckStrategy
     */
    private $startupCheckStrategy;

    /**
     * The number of retry attempts for startup conflicts (port/bind address conflicts).
     *
     * @var null|int
     */
    private $startupConflictRetryAttempts;

    /**
     * The startup check strategy provider.
     *
     * @var StartupCheckStrategyProvider
     */
    private $startupCheckStrategyProvider;

    /**
     * Set the duration of waiting time until the container is treated as started.
     *
     * @param int $timeout the duration to wait
     *
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
     * @param StartupCheckStrategy $strategy the startup check strategy to use
     *
     * @return self
     */
    public function withStartupCheckStrategy($strategy)
    {
        $this->startupCheckStrategy = $strategy;

        return $this;
    }

    /**
     * Set the maximum number of retry attempts for startup conflicts (port/bind address conflicts).
     *
     * @param int $retryAttempts the maximum number of retry attempts
     *
     * @return self
     */
    public function withStartupConflictRetries($retryAttempts)
    {
        $this->startupConflictRetryAttempts = $retryAttempts;

        return $this;
    }

    /**
     * Retrieve the startup timeout for the container.
     *
     * @return null|int
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
     * @param ContainerInstance $instance the container instance for which to get the startup check strategy
     *
     * @return null|StartupCheckStrategy the startup check strategy to be used, or null if none is set
     */
    protected function startupCheckStrategy(/* @noinspection PhpUnusedParameterInspection */ $instance)
    {
        if (null === $this->startupCheckStrategyProvider) {
            $this->startupCheckStrategyProvider = new StartupCheckStrategyProvider();
            $this->registerStartupCheckStrategy($this->startupCheckStrategyProvider);
        }

        if (null !== static::$STARTUP_CHECK_STRATEGY) {
            $strategy = $this->startupCheckStrategyProvider->get(static::$STARTUP_CHECK_STRATEGY);
            if (!$strategy) {
                throw new \LogicException('Startup check strategy not found: '.static::$STARTUP_CHECK_STRATEGY);
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
     * @param StartupCheckStrategyProvider $provider the startup check strategy provider
     */
    protected function registerStartupCheckStrategy($provider)
    {
        try {
            $provider->register('is_running', new IsRunningStartupCheckStrategy());
        } catch (AlreadyExistsStartupStrategyException $e) {
            throw new \LogicException('Startup check strategy with name is_running already exists.', 0, $e);
        }
    }

    /**
     * Retrieve the maximum number of retry attempts for startup conflicts (port/bind address conflicts).
     *
     * @return int
     */
    protected function startupConflictRetryAttempts()
    {
        if (static::$STARTUP_CONFLICT_RETRY_ATTEMPTS !== null) {
            return static::$STARTUP_CONFLICT_RETRY_ATTEMPTS;
        }
        if ($this->startupConflictRetryAttempts) {
            return $this->startupConflictRetryAttempts;
        }

        return self::$DEFAULT_STARTUP_CONFLICT_RETRY_ATTEMPTS;
    }
}
