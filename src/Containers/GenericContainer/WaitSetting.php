<?php

namespace Testcontainers\Containers\GenericContainer;

use LogicException;
use Testcontainers\Containers\ContainerInstance;
use Testcontainers\Containers\WaitStrategy\AlreadyExistsWaitStrategyException;
use Testcontainers\Containers\WaitStrategy\HostPortWaitStrategy;
use Testcontainers\Containers\WaitStrategy\HttpWaitStrategy;
use Testcontainers\Containers\WaitStrategy\LogMessageWaitStrategy;
use Testcontainers\Containers\WaitStrategy\WaitStrategy;
use Testcontainers\Containers\WaitStrategy\WaitStrategyProvider;

/**
 * WaitSetting trait provides the functionality to set and retrieve the wait strategy for the container.
 *
 * Two formats are supported:
 * 1. static variable `$WAIT_STRATEGY`:
 *
 * <code>
 *     class YourContainer extends GenericContainer
 *     {
 *         protected static $WAIT_STRATEGY = 'someWaitStrategy';
 *     }
 * </code>
 *
 * 2. method `withWaitStrategy`:
 *
 * <code>
 *     $container = (new YourContainer('image'))
 *       ->withWaitStrategy(new SomeWaitStrategy());
 * </code>
 */
trait WaitSetting
{
    /**
     * Define the default wait strategy to be used for the container.
     * @var string|null
     */
    protected static $WAIT_STRATEGY;

    /**
     * The wait strategy to be used for the container.
     * @var WaitStrategy|null
     */
    private $waitStrategy;

    /**
     * The wait strategy provider.
     * @var WaitStrategyProvider
     */
    private $waitStrategyProvider;

    /**
     * Set the wait strategy used for waiting for the container to start.
     *
     * @param WaitStrategy $waitStrategy The wait strategy to use.
     * @return self
     */
    public function withWaitStrategy($waitStrategy)
    {
        $this->waitStrategy = $waitStrategy;

        return $this;
    }

    /**
     * Retrieve the wait strategy for the container.
     *
     * This method returns the wait strategy that should be used for the container.
     * If a specific wait strategy is set, it will return that. Otherwise, it will
     * attempt to retrieve the default wait strategy from the provider.
     *
     * @param ContainerInstance $instance The container instance for which to get the wait strategy.
     * @return WaitStrategy|null The wait strategy to be used, or null if none is set.
     */
    protected function waitStrategy(/** @noinspection PhpUnusedParameterInspection */ $instance)
    {
        if ($this->waitStrategyProvider === null) {
            $this->waitStrategyProvider = new WaitStrategyProvider();
            $this->registerWaitStrategy($this->waitStrategyProvider);
        }
        if (static::$WAIT_STRATEGY !== null) {
            $strategy = $this->waitStrategyProvider->get(static::$WAIT_STRATEGY);
            if (!$strategy) {
                throw new LogicException("Wait strategy not found: " . static::$WAIT_STRATEGY);
            }
            return $strategy;
        }
        if ($this->waitStrategy) {
            return $this->waitStrategy;
        }
        return null;
    }

    /**
     * Register a wait strategy.
     *
     * @param WaitStrategyProvider $provider The wait strategy provider.
     * @return void
     */
    protected function registerWaitStrategy($provider)
    {
        try {
            $provider->register('host_port', new HostPortWaitStrategy());
            $provider->register('http', new HttpWaitStrategy());
            $provider->register('log', new LogMessageWaitStrategy());
        } catch (AlreadyExistsWaitStrategyException $e) {
            throw new LogicException("Wait strategy already exists", 0, $e);
        }
    }
}
