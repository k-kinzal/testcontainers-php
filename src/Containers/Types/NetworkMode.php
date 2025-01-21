<?php

namespace Testcontainers\Containers\Types;

/**
 * Represents a network mode for a container.
 *
 * This class encapsulates the network name or driver used by a container and provides
 * convenient access to common network modes (`host`, `bridge`), as well as supporting
 * custom network modes.
 */
class NetworkMode
{
    /**
     * Host network mode.
     *
     * In this mode, the container shares the host's network stack,
     * and the container's ports will be exposed on the host's IP address.
     * No port mapping is required.
     *
     * @var string
     */
    public static $HOST = 'host';

    /**
     * Bridge network mode (default).
     *
     * The container has its own network stack, isolated from the host.
     * Port mapping is typically used to expose container ports to the host.
     *
     * @var string
     */
    public static $BRIDGE = 'bridge';

    /**
     * None network mode.
     *
     * In this mode, the container has no network access.
     *
     * @var string
     */
    public static $NONE = 'none';

    /**
     * The network mode.
     * The name of the network or driver used by the container.
     *
     * @var string
     */
    private $mode;

    /**
     * Initializes a NetworkMode object.
     *
     * @param string $mode The network mode (e.g., 'host', 'bridge', or a custom network name).
     */
    public function __construct($mode)
    {
        $this->mode = $mode;
    }

    /**
     * Creates a NetworkMode object with the host network mode.
     *
     * @return self A NetworkMode object with the host network mode.
     */
    public static function HOST()
    {
        return new self(self::$HOST);
    }

    /**
     * Creates a NetworkMode object with the bridge network mode.
     *
     * @return self A NetworkMode object with the bridge network mode.
     */
    public static function BRIDGE()
    {
        return new self(self::$BRIDGE);
    }

    /**
     * Creates a NetworkMode object with the none network mode.
     *
     * @return self A NetworkMode object with the none network mode.
     */
    public static function NONE()
    {
        return new self(self::$NONE);
    }

    /**
     * Creates a NetworkMode object from a string.
     *
     * @param string $s The network mode string.
     * @return NetworkMode A NetworkMode object representing the given network mode.
     */
    public static function fromString($s)
    {
        return new self($s);
    }

    /**
     * Returns the network mode as a string.
     *
     * @return string The network mode.
     */
    public function toString()
    {
        return $this->mode;
    }

    /**
     * Returns the network mode as a string (alias for toString()).
     *
     * @return string The network mode.
     */
    public function __toString()
    {
        return $this->toString();
    }
}
