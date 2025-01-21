<?php

namespace Testcontainers\Containers\GenericContainer;

trait EnvSetting
{
    /**
     * Define the default environment variables to be used for the container.
     * @var array|null
     */
    protected static $ENVIRONMENTS;

    /**
     * The environment variables to be used for the container.
     * @var array
     */
    private $env = [];

    /**
     * Add an environment variable to the container.
     *
     * @param string $key The name of the environment variable.
     * @param string $value The value of the environment variable.
     * @return self
     */
    public function withEnv($key, $value)
    {
        $this->env[$key] = $value;

        return $this;
    }

    /**
     * Add multiple environment variables to the container.
     *
     * @param array<string, string> $env An associative array where the key is the environment variable name and the value is the environment variable value.
     * @return self
     */
    public function withEnvs($env)
    {
        $this->env = array_merge($this->env, $env);

        return $this;
    }

    /**
     * Retrieve the environment variables for the container.
     *
     * This method returns the environment variables that should be used for the container.
     * If specific environment variables are set, it will return those. Otherwise, it will
     * attempt to retrieve the default environment variables from the provider.
     *
     * @return array|null The environment variables to be used, or null if none are set.
     */
    protected function env()
    {
        if (static::$ENVIRONMENTS) {
            return static::$ENVIRONMENTS;
        }
        if ($this->env) {
            return $this->env;
        }
        return null;
    }
}
