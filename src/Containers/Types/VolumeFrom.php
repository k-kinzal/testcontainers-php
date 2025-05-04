<?php

namespace Testcontainers\Containers\Types;

use LogicException;
use Testcontainers\Exceptions\InvalidFormatException;

/**
 * Represents a volume from.
 *
 * @property-read string $name The name of the container to mount volumes from.
 * @property-read BindMode $mode The mode of the bind (e.g., read-only or read-write).
 */
class VolumeFrom
{
    /**
     * The name of the container to mount volumes from.
     * @var string
     */
    private $name;

    /**
     * The mode of the bind (e.g., read-only or read-write).
     * @var BindMode
     */
    private $mode;

    /**
     * @param string $name
     * @param BindMode $mode
     */
    public function __construct($name, $mode)
    {
        $this->name = $name;
        $this->mode = $mode;
    }

    /**
     * Get the name of the container to mount volumes from.
     *
     * @param string $v The volume from string.
     * @return VolumeFrom
     *
     * @throws InvalidFormatException If the mount format is invalid.
     */
    public static function fromString($v)
    {
        if (strpos($v, ':') === false) {
            return new VolumeFrom($v, BindMode::READ_WRITE());
        } else {
            $parts = explode(':', $v);
            return new VolumeFrom($parts[0], BindMode::fromString($parts[1]));
        }
    }

    /**
     * @param array{
     *    name: string,
     *    mode: BindMode
     * } $arr The array representation of the volume from.
     * @return VolumeFrom
     */
    public static function fromArray($arr)
    {
        return new VolumeFrom($arr['name'], $arr['mode']);
    }

    /**
     * Get the volume from string.
     *
     * @return string
     */
    public function toString()
    {
        return (string) $this;
    }

    public function __toString()
    {
        return $this->name . ':' . $this->mode->toString();
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        if (!property_exists($this, $name)) {
            throw new LogicException('VolumeFrom::' . $name . ' does not exist');
        }
        return $this->$name;
    }
}
