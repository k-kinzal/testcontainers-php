<?php

namespace Testcontainers\Containers\PortStrategy;

/**
 * Provides a registry for port strategies.
 *
 * This class allows for the registration and retrieval of different port strategies
 * by their unique names. It maintains an associative array that maps strategy names
 * to their corresponding `PortStrategy` instances.
 */
class PortStrategyProvider
{
    /**
     * An associative array that maps strategy names to their corresponding PortStrategy instances.
     *
     * @var array<string, PortStrategy> the array of registered port strategies
     */
    private $strategies = [];

    /**
     * Registers a port strategy.
     *
     * This method adds a given port strategy to the list of available strategies.
     *
     * @param string       $name     the name of the port strategy to register
     * @param PortStrategy $strategy the port strategy to register
     *
     * @throws AlreadyExistsPortStrategyException if a strategy with the same name already exists
     */
    public function register($name, $strategy)
    {
        if (isset($this->strategies[$name])) {
            throw new AlreadyExistsPortStrategyException($name);
        }
        $this->strategies[$name] = $strategy;
    }

    /**
     * Retrieves a registered port strategy by its name.
     *
     * This method looks up a port strategy by its name and returns the corresponding
     * PortStrategy instance if it exists. If no strategy is found with the given name,
     * it returns null.
     *
     * @param string $name the name of the port strategy to retrieve
     *
     * @return null|PortStrategy the PortStrategy instance if found, or null if not found
     */
    public function get($name)
    {
        return isset($this->strategies[$name]) ? $this->strategies[$name] : null;
    }
}
