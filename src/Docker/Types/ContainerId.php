<?php

namespace Testcontainers\Docker\Types;

use Testcontainers\Utility\Stringable;

/**
 * Represents the ID of a Docker container.
 *
 * This class is a value object that represents the ID of a Docker container.
 * The container ID is a 64-character hexadecimal string that uniquely identifies
 * a Docker container on the host system.
 */
class ContainerId implements Stringable
{
    /**
     * The ID of the Docker container.
     *
     * @var string
     */
    private $data;

    /**
     * @param string $v
     *
     * @throws \InvalidArgumentException if the container ID is not a valid 64-character hexadecimal string
     */
    public function __construct($v)
    {
        if (!self::isValid($v)) {
            throw new \LogicException('Invalid container ID: `'.$v.'`');
        }

        $this->data = $v;
    }

    public function __toString()
    {
        return $this->data;
    }

    /**
     * Check if the container ID is valid.
     *
     * This method checks if the given value is a valid container ID.
     * A valid container ID is a 64-character hexadecimal string.
     *
     * @param mixed $v the value to check
     *
     * @return bool true if the value is a valid container ID, false otherwise
     */
    public static function isValid($v)
    {
        if (!is_string($v)) {
            return false;
        }
        if (1 !== preg_match('/^[a-f0-9]{12}$/', $v)
            && 1 !== preg_match('/^[a-f0-9]{64}$/', $v)) {
            return false;
        }

        return true;
    }

    /**
     * Create a ContainerId object from a string.
     *
     * @param string $v the container ID
     *
     * @throws \InvalidArgumentException if the container ID is not a valid 64-character hexadecimal string
     *
     * @return ContainerId the ContainerId object
     */
    public static function fromString($v)
    {
        if (!self::isValid($v)) {
            throw new \InvalidArgumentException('Invalid container ID: `'.$v.'`');
        }

        return new self($v);
    }

    /**
     * Get the container ID.
     *
     * @return string the container ID
     */
    public function toString()
    {
        return $this->__toString();
    }
}
