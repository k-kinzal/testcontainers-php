<?php

namespace Testcontainers\Containers\WaitStrategy;

use Exception;

/**
 * Provides a registry for wait strategies.
 *
 * This class allows for the registration and retrieval of different wait strategies
 * by their unique names. It maintains an associative array that maps strategy names
 * to their corresponding `WaitStrategy` instances.
 */
class WaitStrategyProvider
{
    /**
     * An associative array that maps strategy names to their corresponding WaitStrategy instances.
     *
     * @var array<string, WaitStrategy> The array of registered wait strategies.
     */
    private $strategies = [];

    /**
     * Registers a wait strategy.
     *
     * This method adds a given wait strategy to the list of available strategies.
     *
     * @param WaitStrategy $strategy The wait strategy to register.
     *
     * @throws Exception If a strategy with the same name already exists.
     */
    public function register($strategy)
    {
        if (isset($this->strategies[$strategy->getName()])) {
            throw new Exception('Wait strategy with name ' . $strategy->getName() . ' already exists.');
        }
        $this->strategies[$strategy->getName()] = $strategy;
    }

    /**
     * Retrieves a registered wait strategy by its name.
     *
     * This method looks up a wait strategy by its name and returns the corresponding
     * WaitStrategy instance if it exists. If no strategy is found with the given name,
     * it returns null.
     *
     * @param string $name The name of the wait strategy to retrieve.
     * @return WaitStrategy|null The WaitStrategy instance if found, or null if not found.
     */
    public function get($name)
    {
        return $this->strategies[$name] ?: null;
    }
}
