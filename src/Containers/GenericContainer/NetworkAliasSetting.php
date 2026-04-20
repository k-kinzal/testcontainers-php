<?php

namespace Testcontainers\Containers\GenericContainer;

use function Testcontainers\ensure;

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
        /** @psalm-suppress RedundantConditionGivenDocblockType */
        ensure(is_string($alias), '$alias must be string');

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
        /** @psalm-suppress RedundantConditionGivenDocblockType */
        ensure(is_array($aliases), '$aliases must be array');

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
        /** @psalm-suppress RedundantConditionGivenDocblockType */
        ensure(
            static::$NETWORK_ALIASES === null || is_array(static::$NETWORK_ALIASES),
            'static::$NETWORK_ALIASES must be null|array'
        );

        if (static::$NETWORK_ALIASES !== null) {
            return static::$NETWORK_ALIASES;
        }
        if ($this->networkAliases) {
            return $this->networkAliases;
        }

        return [];
    }
}
