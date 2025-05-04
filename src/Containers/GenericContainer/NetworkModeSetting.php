<?php

namespace Testcontainers\Containers\GenericContainer;

use Testcontainers\Containers\Types\NetworkMode;

/**
 * NetworkModeSetting is a trait that provides the ability to set the network mode for a container.
 *
 * Two formats are supported:
 * 1. static variable `$NETWORK_MODE`:
 *
 * <code>
 *     class YourContainer extends GenericContainer
 *     {
 *         protected static $NETWORK_MODE = 'host';
 *     }
 * </code>
 *
 * 2. method `withNetworkMode`:
 *
 * <code>
 *     $container = (new YourContainer('image'))
 *         ->withNetworkMode('host');
 * </code>
 */
trait NetworkModeSetting
{
    /**
     * Define the default network mode to be used for the container.
     *
     * @var null|string
     */
    protected static $NETWORK_MODE;

    /**
     * The network mode to be used for the container.
     *
     * @var null|NetworkMode
     */
    private $networkMode;

    /**
     * Set the network mode for this container, similar to the `--net <name>` option on the Docker CLI.
     *
     * @param NetworkMode $networkMode The network mode, e.g., 'host', 'bridge', 'none', or the name of an existing named network.
     *
     * @return self
     */
    public function withNetworkMode($networkMode)
    {
        $this->networkMode = $networkMode;

        return $this;
    }

    /**
     * Retrieve the network mode to be used for the container.
     *
     * @return null|NetworkMode
     */
    protected function networkMode()
    {
        if (static::$NETWORK_MODE) {
            return NetworkMode::fromString(static::$NETWORK_MODE);
        }
        if ($this->networkMode) {
            return $this->networkMode;
        }

        return null;
    }
}
