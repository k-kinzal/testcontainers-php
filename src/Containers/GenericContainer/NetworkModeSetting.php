<?php

namespace Testcontainers\Containers\GenericContainer;

use Testcontainers\Containers\Types\NetworkMode;

trait NetworkModeSetting
{
    /**
     * Define the default network mode to be used for the container.
     * @var string|null
     */
    protected static $NETWORK_MODE;

    /**
     * The network mode to be used for the container.
     * @var NetworkMode|null
     */
    private $networkMode;

    /**
     * Set the network mode for this container, similar to the `--net <name>` option on the Docker CLI.
     *
     * @param NetworkMode $networkMode The network mode, e.g., 'host', 'bridge', 'none', or the name of an existing named network.
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
     * @return NetworkMode|null
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
