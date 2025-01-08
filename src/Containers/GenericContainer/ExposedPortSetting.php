<?php

namespace Testcontainers\Containers\GenericContainer;

/**
 * ExposedPortSetting is a trait that provides the ability to expose ports on a container.
 *
 * Two formats are supported:
 * 1. static variable `$PORTS`:
 *
 * <code>
 * class YourContainer extends GenericContainer
 * {
 *     protected static $PORTS = [80, 443];
 * }
 * </code>
 *
 * 2. method `withExposedPorts`:
 *
 * <code>
 *     $container = (new YourContainer('image'))
 *        ->withExposedPorts(80);
 * </code>
 */
trait ExposedPortSetting
{
    /**
     * Define the default ports to be exposed by the container.
     * @var int[]|null
     */
    protected static $PORTS;

    /**
     * The ports to be exposed by the container.
     * @var int[]
     */
    private $ports = [];

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
        $this->ports = $ports;

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
    protected function ports()
    {
        if (static::$PORTS) {
            return static::$PORTS;
        }
        if ($this->ports) {
            return $this->ports;
        }
        return null;
    }
}
