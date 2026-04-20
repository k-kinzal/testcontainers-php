<?php

namespace Testcontainers\Containers\GenericContainer;

use function Testcontainers\ensure;

/**
 * UserSetting is a trait that provides the ability to set the user for a container.
 *
 * Two formats are supported:
 * 1. static variable `$USER`:
 *
 * <code>
 * class YourContainer extends GenericContainer
 * {
 *     protected static $USER = 'root';
 * }
 * </code>
 *
 * 2. method `withUser`:
 *
 * <code>
 * $container = (new YourContainer('image'))
 *     ->withUser('root');
 * </code>
 */
trait UserSetting
{
    /**
     * Define the default user to be used for the container.
     *
     * @var null|string
     */
    protected static $USER;

    /**
     * The user to be used for the container.
     *
     * @var null|string
     */
    private $user;

    /**
     * Set the user that the container should run as.
     *
     * @param string $user the user to run the container as (e.g., 'root', 'nobody', '1000:1000')
     *
     * @return self
     */
    public function withUser($user)
    {
        ensure(is_string($user), '$user must be string');

        $this->user = $user;

        return $this;
    }

    /**
     * Retrieve the user for the container.
     *
     * @return null|string
     */
    protected function user()
    {
        ensure(static::$USER === null || is_string(static::$USER), 'static::$USER must be null|string');

        if (static::$USER !== null) {
            return static::$USER;
        }

        return $this->user;
    }
}
