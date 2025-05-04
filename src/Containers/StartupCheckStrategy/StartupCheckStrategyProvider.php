<?php

namespace Testcontainers\Containers\StartupCheckStrategy;

/**
 * A provider for startup check strategies.
 *
 * This class provides a way to register and retrieve startup check strategies by their name.
 */
class StartupCheckStrategyProvider
{
    /**
     * An associative array that maps strategy names to their corresponding StartupCheckStrategy instances.
     *
     * @var array<string, StartupCheckStrategy> The array of registered startup check strategies.
     */
    private $strategies = [];

    /**
     * Registers a wait strategy.
     *
     * This method adds a given wait strategy to the list of available strategies.
     *
     * @param string $name The name of the wait strategy to register.
     * @param StartupCheckStrategy $strategy The wait strategy to register.
     * @return void
     *
     * @throws AlreadyExistsStartupStrategyException If a strategy with the same name already exists.
     */
    public function register($name, $strategy)
    {
        if (isset($this->strategies[$name])) {
            throw new AlreadyExistsStartupStrategyException($name);
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
     * @param string $name The name of the wait strategy to retrieve.
     * @return StartupCheckStrategy|null The WaitStrategy instance if found, or null if not found.
     */
    public function get($name)
    {
        return isset($this->strategies[$name]) ? $this->strategies[$name] : null;
    }
}
