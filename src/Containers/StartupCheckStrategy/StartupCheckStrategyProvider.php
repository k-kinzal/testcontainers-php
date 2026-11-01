<?php

namespace Testcontainers\Containers\StartupCheckStrategy;

use Testcontainers\Utility\Ensure;

/**
 * A provider for startup check strategies.
 *
 * This class provides a way to register and retrieve startup check strategies by their name.
 */
class StartupCheckStrategyProvider
{
    use Ensure;

    /**
     * An associative array that maps strategy names to their corresponding StartupCheckStrategy instances.
     *
     * @var array<string, StartupCheckStrategy> the array of registered startup check strategies
     */
    private $strategies = [];

    /**
     * Registers a wait strategy.
     *
     * This method adds a given wait strategy to the list of available strategies.
     *
     * @param string               $name     the name of the wait strategy to register
     * @param StartupCheckStrategy $strategy the wait strategy to register
     * @psalm-param mixed $name
     * @psalm-param mixed $strategy
     *
     * @throws AlreadyExistsStartupStrategyException if a strategy with the same name already exists
     */
    public function register($name, $strategy)
    {
        self::ensureNonEmptyString($name);

        self::ensureInstanceOf($strategy, StartupCheckStrategy::class);

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
     * @param string $name the name of the wait strategy to retrieve
     * @psalm-param mixed $name
     *
     * @return null|StartupCheckStrategy the WaitStrategy instance if found, or null if not found
     */
    public function get($name)
    {
        self::ensureString($name);

        return isset($this->strategies[$name]) ? $this->strategies[$name] : null;
    }
}
