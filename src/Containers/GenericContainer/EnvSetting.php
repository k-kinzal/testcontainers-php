<?php

namespace Testcontainers\Containers\GenericContainer;

/**
 * EnvSetting is a trait that provides the ability to add environment variables to a container.
 *
 * Two formats are supported:
 * 1. static variable `$ENVIRONMENTS` or `$ENV`:
 *
 * <code>
 * class YourContainer extends GenericContainer
 * {
 *     protected static $ENVIRONMENTS = [
 *         'ENV1' => 'value1',
 *         'ENV2' => 'value2',
 *     ];
 * }
 * </code>
 *
 * 2. method `withEnv` or `withEnvs`:
 *
 * <code>
 * $container = (new YourContainer('image'))
 *     ->withEnv('ENV1', 'value1')
 *     ->withEnv('ENV2', 'value2');
 * </code>
 */
trait EnvSetting
{
    /**
     * Define the default environment variables to be used for the container.
     *
     * @var null|array<string, string>
     * @psalm-var mixed
     */
    protected static $ENVIRONMENTS;

    /**
     * Define the default environment variables to be used for the container. Alias for `ENVIRONMENTS`.
     *
     * @var null|array<string, string>
     * @psalm-var mixed
     */
    protected static $ENV;

    /**
     * The environment variables to be used for the container.
     *
     * @var array<string, string>
     */
    private $env = [];

    /**
     * Add an environment variable to the container.
     *
     * @param string $key   the name of the environment variable
     * @param string $value the value of the environment variable
     * @psalm-param mixed $key
     * @psalm-param mixed $value
     *
     * @return self
     */
    public function withEnv($key, $value)
    {
        self::ensureString($key);
        self::ensureString($value);

        $this->env[$key] = $value;

        return $this;
    }

    /**
     * Add multiple environment variables to the container.
     *
     * @param array<string, string> $env an associative array where the key is the environment variable name and the value is the environment variable value
     * @psalm-param mixed $env
     *
     * @return self
     */
    public function withEnvs($env)
    {
        self::ensureStringMap($env);

        $this->env = $env;

        return $this;
    }

    /**
     * Retrieve the environment variables for the container.
     *
     * This method returns the environment variables that should be used for the container.
     * If specific environment variables are set, it will return those. Otherwise, it will
     * attempt to retrieve the default environment variables from the provider.
     *
     * @return array<string, string> the environment variables to be used for the container
     */
    protected function env()
    {
        if (static::$ENVIRONMENTS !== null) {
            self::ensureStringMap(static::$ENVIRONMENTS);

            return static::$ENVIRONMENTS;
        }
        if (static::$ENV !== null) {
            self::ensureStringMap(static::$ENV);

            return static::$ENV;
        }
        if ($this->env) {
            return $this->env;
        }

        return [];
    }
}
