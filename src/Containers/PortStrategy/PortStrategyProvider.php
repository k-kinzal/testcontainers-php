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
     * @var array<string, PortStrategy> The array of registered port strategies.
     */
    private $strategies = [];

    /**
     * Registers a port strategy.
     *
     * This method adds a given port strategy to the list of available strategies.
     *
     * @param string $name The name of the port strategy to register.
     * @param PortStrategy $strategy The port strategy to register.
     * @return void
     *
     * @throws AlreadyExistsPortStrategyException If a strategy with the same name already exists.
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
     * @param string $name The name of the port strategy to retrieve.
     * @return PortStrategy|null The PortStrategy instance if found, or null if not found.
     */
    public function get($name)
    {
        return isset($this->strategies[$name]) ? $this->strategies[$name] : null;
    }
}
