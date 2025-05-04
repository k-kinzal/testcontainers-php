<?php

namespace Testcontainers\Docker\Types;

use InvalidArgumentException;
use LogicException;
use Testcontainers\Utility\Stringable;

/**
 * Represents a Docker network ID.
 *
 * A network ID is a 64-character hexadecimal string.
 */
class NetworkId implements Stringable
{
    /**
     * The ID of the Docker container.
     * @var string
     */
    private $data;

    /**
     * @param string $v
     *
     * @throws InvalidArgumentException If the network ID is not a valid 64-character hexadecimal string.
     */
    public function __construct($v)
    {
        if (!self::isValid($v)) {
            throw new LogicException('Invalid network ID: `' . $v . '`');
        }

        $this->data = $v;
    }

    /**
     * Check if the network ID is valid.
     *
     * This method checks if the given value is a valid network ID.
     * A valid network ID is a 64-character hexadecimal string.
     *
     * @param mixed $v The value to check.
     * @return bool True if the value is a valid network ID, false otherwise.
     */
    public static function isValid($v)
    {
        if (!is_string($v)) {
            return false;
        }
        if (preg_match('/^[a-f0-9]{12}$/', $v) !== 1
            && preg_match('/^[a-f0-9]{64}$/', $v) !== 1) {
            return false;
        }
        return true;
    }

    /**
     * Create a NetworkId object from a string.
     *
     * @param string $v The network ID.
     * @return NetworkId The NetworkId object.
     *
     * @throws InvalidArgumentException If the network ID is not valid.
     */
    public static function fromString($v)
    {
        if (!self::isValid($v)) {
            throw new InvalidArgumentException('Invalid network ID: `' . $v . '`');
        }
        return new self($v);
    }

    /**
     * Get the network ID.
     *
     * @return string The network ID.
     */
    public function toString()
    {
        return $this->__toString();
    }

    /**
     * @inheritDoc
     */
    public function __toString()
    {
        return $this->data;
    }
}
