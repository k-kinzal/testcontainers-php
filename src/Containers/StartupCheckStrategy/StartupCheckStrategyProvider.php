<?php

namespace Testcontainers\Containers\StartupCheckStrategy;

/**
 * A provider for startup check strategies.
 *
 * This class provides a way to register and retrieve startup check strategies by their name.
 */
class StartupCheckStrategyProvider
{
    private $strategies = [];

    /**
     * Registers a wait strategy.
     *
     * This method adds a given wait strategy to the list of available strategies.
     *
     * @param StartupCheckStrategy $strategy The wait strategy to register.
     *
     * @throws AlreadyExistsStartupStrategyException If a strategy with the same name already exists.
     */
    public function register($strategy)
    {
        if (isset($this->strategies[$strategy->getName()])) {
            throw new AlreadyExistsStartupStrategyException($strategy->getName());
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
     * @return StartupCheckStrategy|null The WaitStrategy instance if found, or null if not found.
     */
    public function get($name)
    {
        return isset($this->strategies[$name]) ? $this->strategies[$name] : null;
    }
}
