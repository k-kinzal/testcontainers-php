<?php

namespace Testcontainers\Containers\Types;

use Testcontainers\Exceptions\InvalidFormatException;
use Testcontainers\Utility\Stringable;

/**
 * Represents a volume from.
 *
 * @property string   $name The name of the container to mount volumes from.
 * @property BindMode $mode The mode of the bind (e.g., read-only or read-write).
 */
class VolumeFrom implements Stringable
{
    /**
     * The name of the container to mount volumes from.
     *
     * @var string
     */
    private $name;

    /**
     * The mode of the bind (e.g., read-only or read-write).
     *
     * @var BindMode
     */
    private $mode;

    /**
     * @param string   $name
     * @param BindMode $mode
     */
    public function __construct($name, $mode)
    {
        $this->name = $name;
        $this->mode = $mode;
    }

    public function __toString()
    {
        return $this->name.':'.$this->mode->toString();
    }

    /**
     * @param string $name
     *
     * @return mixed
     */
    public function __get($name)
    {
        if (!property_exists($this, $name)) {
            throw new \LogicException('VolumeFrom::'.$name.' does not exist');
        }

        return $this->{$name};
    }

    /**
     * Get the name of the container to mount volumes from.
     *
     * @param string $v the volume from string
     *
     * @throws InvalidFormatException if the mount format is invalid
     *
     * @return VolumeFrom
     */
    public static function fromString($v)
    {
        if (false === strpos($v, ':')) {
            return new VolumeFrom($v, BindMode::READ_WRITE());
        }
        $parts = explode(':', $v);

        return new VolumeFrom($parts[0], BindMode::fromString($parts[1]));
    }

    /**
     * @param array{
     *    name: string,
     *    mode: BindMode
     * } $arr The array representation of the volume from
     *
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
}
