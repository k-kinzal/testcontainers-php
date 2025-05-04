<?php

namespace Testcontainers\Containers\GenericContainer;

/**
 * NetworkAliasSetting is a trait that provides the ability to add network aliases to a container.
 *
 * Two formats are supported:
 * 1. static variable `$NETWORK_ALIASES`:
 *
 * <code>
 *     class YourContainer extends GenericContainer
 *     {
 *         protected static $NETWORK_ALIASES = ['my-service'];
 *     }
 * </code>
 *
 * 2. method `withNetworkAliases`:
 *
 * <code>
 *     $container = (new YourContainer('image'))
 *         ->withNetworkAliases(['my-service']);
 * </code>
 */
trait NetworkAliasSetting
{
    /**
     * Define the default network aliases to be used for the container.
     *
     * @var null|string[]
     */
    protected static $NETWORK_ALIASES;

    /**
     * The network aliases to be used for the container.
     *
     * @var string[]
     */
    private $networkAliases = [];

    /**
     * Set the network alias for this container, similar to the `--network-alias <my-service>` option on the Docker CLI.
     *
     * @param string $alias the network alias to set
     *
     * @return self
     */
    public function withNetworkAlias($alias)
    {
        $this->networkAliases[] = $alias;

        return $this;
    }

    /**
     * Set the network aliases for this container, similar to the `--network-alias <my-service>` option on the Docker CLI.
     *
     * @param string[] $aliases the network aliases to set
     *
     * @return self
     */
    public function withNetworkAliases($aliases)
    {
        $this->networkAliases = $aliases;

        return $this;
    }

    /**
     * Retrieve the network aliases to be used for the container.
     *
     * @return string[]
     */
    protected function networkAliases()
    {
        if (static::$NETWORK_ALIASES) {
            return static::$NETWORK_ALIASES;
        }
        if ($this->networkAliases) {
            return $this->networkAliases;
        }

        return [];
    }
}
