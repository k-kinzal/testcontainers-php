<?php

namespace Testcontainers\Containers\WaitStrategy;

use Testcontainers\Utility\Ensure;

/**
 * Provides a registry for wait strategies.
 *
 * This class allows for the registration and retrieval of different wait strategies
 * by their unique names. It maintains an associative array that maps strategy names
 * to their corresponding `WaitStrategy` instances.
 * @api
 */
class WaitStrategyProvider
{
    use Ensure;

    /**
     * An associative array that maps strategy names to their corresponding WaitStrategy instances.
     *
     * @var array<string, WaitStrategy> the array of registered wait strategies
     */
    private $strategies = [];

    /**
     * Registers a wait strategy.
     *
     * This method adds a given wait strategy to the list of available strategies.
     *
     * @param string       $name     the name of the wait strategy to register
     * @param WaitStrategy $strategy the wait strategy to register
     * @psalm-param mixed $name
     * @psalm-param mixed $strategy
     *
     * @throws AlreadyExistsWaitStrategyException if a strategy with the same name already exists
     * @return void
     */
    public function register($name, $strategy)
    {
        self::ensureNonEmptyString($name);
        self::ensureInstanceOf($strategy, WaitStrategy::class);

        if (isset($this->strategies[$name])) {
            throw new AlreadyExistsWaitStrategyException($name);
        }
        $this->strategies[$name] = $strategy;
    }

    /**
     * Retrieves a registered wait strategy by its name.
     *
     * This method looks up a wait strategy by its name and returns the corresponding
     * WaitStrategy instance if it exists. If no strategy is found with the given name,
     * it returns null.
     *
     * @param string $name the name of the wait strategy to retrieve
     * @psalm-param mixed $name
     *
     * @return null|WaitStrategy the WaitStrategy instance if found, or null if not found
     */
    public function get($name)
    {
        self::ensureString($name);

        return isset($this->strategies[$name]) ? $this->strategies[$name] : null;
    }
}
