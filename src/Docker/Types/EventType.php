<?php

namespace Testcontainers\Docker\Types;

use Testcontainers\Exceptions\InvalidFormatException;
use Testcontainers\Utility\Ensure;
use Testcontainers\Utility\Stringable;

/**
 * Represents the type of the object that generated a Docker event.
 * @api
 */
class EventType implements Stringable
{
    use Ensure;

    /**
     * The event type that the builder generates.
     *
     * @var string
     * @psalm-var mixed
     */
    public static $BUILDER = 'builder';

    /**
     * The event type that configs generate.
     *
     * @var string
     * @psalm-var mixed
     */
    public static $CONFIG = 'config';

    /**
     * The event type that containers generate.
     *
     * @var string
     * @psalm-var mixed
     */
    public static $CONTAINER = 'container';

    /**
     * The event type that the daemon generates.
     *
     * @var string
     * @psalm-var mixed
     */
    public static $DAEMON = 'daemon';

    /**
     * The event type that images generate.
     *
     * @var string
     * @psalm-var mixed
     */
    public static $IMAGE = 'image';

    /**
     * The event type that networks generate.
     *
     * @var string
     * @psalm-var mixed
     */
    public static $NETWORK = 'network';

    /**
     * The event type that nodes generate.
     *
     * @var string
     * @psalm-var mixed
     */
    public static $NODE = 'node';

    /**
     * The event type that plugins generate.
     *
     * @var string
     * @psalm-var mixed
     */
    public static $PLUGIN = 'plugin';

    /**
     * The event type that secrets generate.
     *
     * @var string
     * @psalm-var mixed
     */
    public static $SECRET = 'secret';

    /**
     * The event type that services generate.
     *
     * @var string
     * @psalm-var mixed
     */
    public static $SERVICE = 'service';

    /**
     * The event type that volumes generate.
     *
     * @var string
     * @psalm-var mixed
     */
    public static $VOLUME = 'volume';

    /**
     * The type of the object that generated the event.
     *
     * @var string
     */
    private $type;

    /**
     * Create a EventType instance.
     *
     * @param string $type
     * @psalm-param mixed $type
     */
    public function __construct($type)
    {
        self::ensureOneOf($type, self::values());

        $this->type = $type;
    }

    public function __toString()
    {
        return $this->toString();
    }

    /**
     * Create a new instance of `EventType` with the `BUILDER` type.
     *
     * @return self
     */
    public static function BUILDER()
    {
        return new self(static::$BUILDER);
    }

    /**
     * Create a new instance of `EventType` with the `CONFIG` type.
     *
     * @return self
     */
    public static function CONFIG()
    {
        return new self(static::$CONFIG);
    }

    /**
     * Create a new instance of `EventType` with the `CONTAINER` type.
     *
     * @return self
     */
    public static function CONTAINER()
    {
        return new self(static::$CONTAINER);
    }

    /**
     * Create a new instance of `EventType` with the `DAEMON` type.
     *
     * @return self
     */
    public static function DAEMON()
    {
        return new self(static::$DAEMON);
    }

    /**
     * Create a new instance of `EventType` with the `IMAGE` type.
     *
     * @return self
     */
    public static function IMAGE()
    {
        return new self(static::$IMAGE);
    }

    /**
     * Create a new instance of `EventType` with the `NETWORK` type.
     *
     * @return self
     */
    public static function NETWORK()
    {
        return new self(static::$NETWORK);
    }

    /**
     * Create a new instance of `EventType` with the `NODE` type.
     *
     * @return self
     */
    public static function NODE()
    {
        return new self(static::$NODE);
    }

    /**
     * Create a new instance of `EventType` with the `PLUGIN` type.
     *
     * @return self
     */
    public static function PLUGIN()
    {
        return new self(static::$PLUGIN);
    }

    /**
     * Create a new instance of `EventType` with the `SECRET` type.
     *
     * @return self
     */
    public static function SECRET()
    {
        return new self(static::$SECRET);
    }

    /**
     * Create a new instance of `EventType` with the `SERVICE` type.
     *
     * @return self
     */
    public static function SERVICE()
    {
        return new self(static::$SERVICE);
    }

    /**
     * Create a new instance of `EventType` with the `VOLUME` type.
     *
     * @return self
     */
    public static function VOLUME()
    {
        return new self(static::$VOLUME);
    }

    /**
     * Create a new instance of `EventType` from a string representation.
     *
     * @param string $type the string representation of the type
     * @psalm-param mixed $type
     *
     * @throws InvalidFormatException if the provided type is not valid
     *
     * @return self
     */
    public static function fromString($type)
    {
        self::ensureOneOf($type, self::values());

        return new self($type);
    }

    /**
     * Get all valid values.
     *
     * @return string[]
     */
    public static function values()
    {
        $values = [
            static::$BUILDER,
            static::$CONFIG,
            static::$CONTAINER,
            static::$DAEMON,
            static::$IMAGE,
            static::$NETWORK,
            static::$NODE,
            static::$PLUGIN,
            static::$SECRET,
            static::$SERVICE,
            static::$VOLUME,
        ];
        self::ensureStringArray($values);

        return $values;
    }

    /**
     * Check if the type is `BUILDER`.
     *
     * @return bool true if the type is `BUILDER`, false otherwise
     */
    public function isBuilder()
    {
        return $this->type === static::$BUILDER;
    }

    /**
     * Check if the type is `CONFIG`.
     *
     * @return bool true if the type is `CONFIG`, false otherwise
     */
    public function isConfig()
    {
        return $this->type === static::$CONFIG;
    }

    /**
     * Check if the type is `CONTAINER`.
     *
     * @return bool true if the type is `CONTAINER`, false otherwise
     */
    public function isContainer()
    {
        return $this->type === static::$CONTAINER;
    }

    /**
     * Check if the type is `DAEMON`.
     *
     * @return bool true if the type is `DAEMON`, false otherwise
     */
    public function isDaemon()
    {
        return $this->type === static::$DAEMON;
    }

    /**
     * Check if the type is `IMAGE`.
     *
     * @return bool true if the type is `IMAGE`, false otherwise
     */
    public function isImage()
    {
        return $this->type === static::$IMAGE;
    }

    /**
     * Check if the type is `NETWORK`.
     *
     * @return bool true if the type is `NETWORK`, false otherwise
     */
    public function isNetwork()
    {
        return $this->type === static::$NETWORK;
    }

    /**
     * Check if the type is `NODE`.
     *
     * @return bool true if the type is `NODE`, false otherwise
     */
    public function isNode()
    {
        return $this->type === static::$NODE;
    }

    /**
     * Check if the type is `PLUGIN`.
     *
     * @return bool true if the type is `PLUGIN`, false otherwise
     */
    public function isPlugin()
    {
        return $this->type === static::$PLUGIN;
    }

    /**
     * Check if the type is `SECRET`.
     *
     * @return bool true if the type is `SECRET`, false otherwise
     */
    public function isSecret()
    {
        return $this->type === static::$SECRET;
    }

    /**
     * Check if the type is `SERVICE`.
     *
     * @return bool true if the type is `SERVICE`, false otherwise
     */
    public function isService()
    {
        return $this->type === static::$SERVICE;
    }

    /**
     * Check if the type is `VOLUME`.
     *
     * @return bool true if the type is `VOLUME`, false otherwise
     */
    public function isVolume()
    {
        return $this->type === static::$VOLUME;
    }

    /**
     * Get the string representation of the type.
     *
     * @return string the string representation of the type
     */
    public function toString()
    {
        return $this->type;
    }
}
