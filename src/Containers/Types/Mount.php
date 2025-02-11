<?php

namespace Testcontainers\Containers\Types;

use InvalidArgumentException;
use LogicException;
use Testcontainers\Exceptions\InvalidFormatException;

/**
 * Represents a mount.
 *
 * @property-read null|string $type The mount type.
 * @property-read null|string $source The source of the mount.
 * @property-read string $destination The destination of the mount.
 * @property-read null|string $subpath The path in the container at which to mount the volume.
 * @property-read bool $readonly Whether the mount should be read-only.
 * @property-read bool $nocopy Whether the mount should be created with a nocopy option.
 * @property-read array<string, string> $opt An array of key-value pairs that define options for the mount.
 */
class Mount
{
    /**
     * Mount type is “bind” or “volume”.
     * @var null|string
     */
    private $type;

    /**
     * The source of the mount. For named volumes, this is the name of the volume.
     * @var null|string
     */
    private $source;

    /**
     * The destination of the mount.
     * @var string
     */
    private $destination;

    /**
     * The path in the container at which to mount the volume.
     * @var null|string
     */
    private $subpath;

    /**
     * Whether the mount should be read-only.
     * @var bool
     */
    private $readonly;

    /**
     * Whether the mount should be created with a nocopy option.
     * @var bool
     */
    private $nocopy;

    /**
     * An array of key-value pairs that define options for the mount.
     * @var array<string, string>
     */
    private $opt;

    /**
     * @param null|string $type
     * @param null|string $source
     * @param string $destination
     * @param null|string $subpath
     * @param null|bool $readonly
     * @param null|bool $nocopy
     * @param null|array<string, string> $opt
     */
    public function __construct($type, $source, $destination, $subpath, $readonly, $nocopy, $opt)
    {
        $this->type = $type;
        $this->source = $source;
        $this->destination = $destination;
        $this->subpath = $subpath;
        $this->readonly = $readonly ?: false;
        $this->nocopy = $nocopy ?: false;
        $this->opt = $opt ?: [];
    }

    /**
     * Create a Mount object from a string.
     *
     * @param string $v The mount string.
     * @return Mount The Mount object.
     *
     * @throws InvalidFormatException If the format is invalid.
     */
    public static function fromString($v)
    {
        if (strpos($v, ':') > 0 || strpos($v, '=') === false) {
            return self::fromVolumeString($v);
        } else {
            return self::fromMountString($v);
        }
    }

    /**
     * Create a Mount object from a volume string.
     *
     * @param string $v The volume string.
     * @return Mount The Mount object.
     */
    public static function fromVolumeString($v)
    {
        $parts = explode(':', $v);

        $source = null;
        $subpath = null;
        $readonly = false;
        if (count($parts) === 1) {
            $destination = $parts[0];
        } else {
            $source = $parts[0];
            $destination = $parts[1];
            if (isset($parts[2])) {
                // readonly or volume-nocopy is not working.
                // See: https://docs.docker.com/engine/storage/volumes/#options-for---volume
                $readonly = $parts[2] === 'ro';
            }
        }
        return new self('bind', $source, $destination, $subpath, $readonly, false, []);
    }

    /**
     * Create a Mount object from a mount string.
     *
     * @param string $v The mount string.
     * @return Mount The Mount object.
     *
     * @throws InvalidFormatException If the format is invalid.
     */
    public static function fromMountString($v)
    {
        $parts = explode(',', $v);
        if (count($parts) < 1) {
            throw new InvalidFormatException($v, 'type=<type>[,src=<volume-name>],dst=<mount-path>[,<key>=<value>...]');
        }
        $type = 'bind';
        $source = null;
        $destination = null;
        $subpath = null;
        $readonly = false;
        $nocopy = false;
        $opt = [];
        foreach ($parts as $part) {
            $subParts = explode('=', $part);
            switch ($subParts[0]) {
                case 'type':
                    $type = $subParts[1];
                    break;
                case 'source':
                case 'src':
                    $source = $subParts[1];
                    break;
                case 'destination':
                case 'dst':
                case 'target':
                    $destination = $subParts[1];
                    break;
                case 'volume-subpath':
                    $subpath = $subParts[1];
                    break;
                case 'readonly':
                case 'ro':
                    $readonly = true;
                    break;
                case 'volume-nocopy':
                    $nocopy = true;
                    break;
                case 'volume-opt':
                    // TODO: Implement volume-opt on Mount
                    throw new LogicException('unimplemented');
                default:
                    throw new InvalidFormatException($subParts[0], ['type', 'source', 'src', 'destination', 'dst', 'target', 'volume-subpath', 'readonly', 'ro', 'volume-nocopy', 'volume-opt']);
            }
        }

        if (!isset($destination) || !is_string($destination)) {
            throw new InvalidFormatException($v, 'type=<type>[,src=<volume-name>],dst=<mount-path>[,<key>=<value>...]');
        }

        return new self($type, $source, $destination, $subpath, $readonly, $nocopy, $opt);
    }

    /**
     * Create a Mount object from an array.
     *
     * @param array{
     *     type?: string,
     *     source?: string,
     *     src?: string,
     *     destination?: string,
     *     dst?: string,
     *     target?: string,
     *     subpath?: string,
     *     readonly?: bool,
     *     ro?: bool,
     *     nocopy?: bool,
     *     opt?: array<string, string>
     * } $v The mount array.
     * @return Mount The Mount object.
     */
    public static function fromArray($v)
    {
        $type = isset($v['type']) ? $v['type'] : null;
        $source = isset($v['source']) ? $v['source'] : null;
        if ($source === null) {
            $source = isset($v['src']) ? $v['src'] : null;
        }
        $destination = isset($v['destination']) ? $v['destination'] : null;
        if ($destination === null) {
            $destination = isset($v['dst']) ? $v['dst'] : null;
        }
        if ($destination === null) {
            $destination = isset($v['target']) ? $v['target'] : null;
        }
        if ($destination === null) {
            throw new InvalidArgumentException('Invalid mount configuration: destination is required');
        }
        $subpath = isset($v['subpath']) ? $v['subpath'] : null;
        $readonly = isset($v['readonly']) ? $v['readonly'] : false;
        if ($readonly === false) {
            $readonly = isset($v['ro']) ? $v['ro'] : false;
        }
        $nocopy = isset($v['nocopy']) ? $v['nocopy'] : false;
        $opt = isset($v['opt']) ? $v['opt'] : [];

        return new self(
            $type,
            $source,
            $destination,
            $subpath,
            $readonly,
            $nocopy,
            $opt
        );
    }

    /**
     * Get the mount type.
     *
     * @return null|string The mount type.
     */
    public function toString()
    {
        return (string) $this;
    }

    /**
     * Get the mount type.
     *
     * @return string The mount type.
     */
    public function __toString()
    {
        $parts = [];
        if ($this->type !== null) {
            $parts[] = 'type=' . $this->type;
        }
        if ($this->source !== null) {
            $parts[] = 'source=' . $this->source;
        }
        $parts[] = 'destination=' . $this->destination;
        if ($this->subpath !== null) {
            $parts[] = 'volume-subpath=' . $this->subpath;
        }
        if ($this->readonly) {
            $parts[] = 'readonly';
        }
        if ($this->nocopy) {
            $parts[] = 'volume-nocopy';
        }
        foreach ($this->opt as $key => $value) {
            $parts[] = 'volume-opt=' . $key . '=' . $value;
        }
        return implode(',', $parts);
    }

    /**
     * Get the value of a property.
     *
     * @param string $name The name of the property.
     * @return mixed The value of the property.
     */
    public function __get($name)
    {
        if (!property_exists($this, $name)) {
            throw new LogicException('Mount::' . $name . ' does not exist');
        }
        return $this->$name;
    }
}
