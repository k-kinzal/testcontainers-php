<?php

namespace Testcontainers\Containers\GenericContainer;

/**
 * ExposedPortSetting is a trait that provides the ability to expose ports on a container.
 *
 * Two formats are supported:
 * 1. static variable `$EXPOSED_PORTS`:
 *
 * <code>
 * class YourContainer extends GenericContainer
 * {
 *     protected static $EXPOSED_PORTS = [80, 443];
 * }
 * </code>
 *
 * 2. method `withExposedPorts`:
 *
 * <code>
 * $container = (new YourContainer('image'))
 *     ->withExposedPorts(80);
 * </code>
 */
trait ExposedPortSetting
{
    /**
     * Define the default ports to be exposed by the container.
     * @var int[]|null
     */
    protected static $EXPOSED_PORTS;

    /**
     * The ports to be exposed by the container.
     * @var int[]
     */
    private $exposedPorts = [];

    /**
     * Set the port that this container listens on.
     *
     * @param int|string $port The port to expose.
     * @return self
     */
    public function withExposedPort($port)
    {
        if (is_string($port)) {
            $port = intval($port);
        }
        $this->exposedPorts[] = $port;

        return $this;
    }

    /**
     * Set the ports that this container listens on.
     *
     * @param array|int|string $ports The ports to expose. Can be a single port, a range of ports, or an array of ports.
     * @return self
     */
    public function withExposedPorts($ports)
    {
        if (is_int($ports)) {
            $ports = [$ports];
        }
        if (is_string($ports)) {
            $ports = [intval($ports)];
        }
        $this->exposedPorts = $ports;

        return $this;
    }

    /**
     * Retrieve the ports to be exposed by the container.
     *
     * This method returns the ports that should be exposed by the container.
     * If specific ports are set, it will return those. Otherwise, it will
     * attempt to retrieve the default ports from the provider.
     *
     * @return int[]|null The ports to be exposed, or null if none are set.
     */
    protected function exposedPorts()
    {
        if (static::$EXPOSED_PORTS) {
            return static::$EXPOSED_PORTS;
        }
        if ($this->exposedPorts) {
            return $this->exposedPorts;
        }
        return null;
    }
}
