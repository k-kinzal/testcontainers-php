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
     * Define the default ports to be exposed by the container. Alias for `EXPOSED_PORTS`.
     * @var int[]|null
     */
    protected static $EXPOSE;

    /**
     * Define the default ports to be exposed by the container. Alias for `EXPOSED_PORTS`.
     * @var int[]|null
     */
    protected static $PORTS;

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
     * Set the port that this container listens on. Alias for `withExposedPort`.
     *
     * @param int|string $port The port to expose.
     * @return self
     */
    public function withExpose($port)
    {
        return $this->withExposedPort($port);
    }

    /**
     * Set the port that this container listens on. Alias for `withExposedPort`.
     *
     * @param int|string $port The port to expose.
     * @return self
     */
    public function withPort($port)
    {
        return $this->withExposedPort($port);
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
     * Set the ports that this container listens on. Alias for `withExposedPorts`.
     *
     * @param array|int|string $ports The ports to expose. Can be a single port, a range of ports, or an array of ports.
     * @return self
     */
    public function withExposes($ports)
    {
        return $this->withExposedPorts($ports);
    }

    /**
     * Set the ports that this container listens on. Alias for `withExposedPorts`.
     *
     * @param array|int|string $ports The ports to expose. Can be a single port, a range of ports, or an array of ports.
     * @return self
     */
    public function withPorts($ports)
    {
        return $this->withExposedPorts($ports);
    }

    /**
     * Retrieve the ports to be exposed by the container.
     *
     * This method checks for ports defined in the following order:
     * 1. Static variable `$EXPOSED_PORTS`
     * 2. Static variable `$EXPOSE`
     * 3. Static variable `$PORTS`
     * 4. Instance variable `$exposedPorts`
     *
     * @return int[] The list of ports to be exposed.
     */
    protected function exposedPorts()
    {
        if (static::$EXPOSED_PORTS) {
            return static::$EXPOSED_PORTS;
        }
        if (static::$EXPOSE) {
            return static::$EXPOSE;
        }
        if (static::$PORTS) {
            return static::$PORTS;
        }
        if ($this->exposedPorts) {
            return $this->exposedPorts;
        }
        return [];
    }
}
