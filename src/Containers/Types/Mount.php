<?php

namespace Testcontainers\Containers\Types;

use Testcontainers\Utility\Ensure;
use Testcontainers\Exceptions\InvalidFormatException;
use Testcontainers\Utility\Stringable;

/**
 * Represents a mount.
 *
 * @property null|string           $type        The mount type.
 * @property null|string           $source      The source of the mount.
 * @property string                $destination The destination of the mount.
 * @property null|string           $subpath     The path in the container at which to mount the volume.
 * @property bool                  $readonly    Whether the mount should be read-only.
 * @property bool                  $nocopy      Whether the mount should be created with a nocopy option.
 * @property array<string, string> $opt         An array of key-value pairs that define options for the mount.
 */
class Mount implements Stringable
{
    use Ensure;

    /**
     * Mount type is “bind” or “volume”.
     *
     * @var null|string
     */
    private $type;

    /**
     * The source of the mount. For named volumes, this is the name of the volume.
     *
     * @var null|string
     */
    private $source;

    /**
     * The destination of the mount.
     *
     * @var string
     */
    private $destination;

    /**
     * The path in the container at which to mount the volume.
     *
     * @var null|string
     */
    private $subpath;

    /**
     * Whether the mount should be read-only.
     *
     * @var bool
     */
    private $readonly;

    /**
     * Whether the mount should be created with a nocopy option.
     *
     * @var bool
     */
    private $nocopy;

    /**
     * An array of key-value pairs that define options for the mount.
     *
     * @var array<string, string>
     */
    private $opt;

    /**
     * @param null|string                $type
     * @param null|string                $source
     * @param string                     $destination
     * @param null|string                $subpath
     * @param null|bool                  $readonly
     * @param null|bool                  $nocopy
     * @param null|array<string, string> $opt
     * @psalm-param mixed $type
     * @psalm-param mixed $source
     * @psalm-param mixed $destination
     * @psalm-param mixed $subpath
     * @psalm-param mixed $readonly
     * @psalm-param mixed $nocopy
     * @psalm-param mixed $opt
     */
    public function __construct($type, $source, $destination, $subpath, $readonly, $nocopy, $opt)
    {
        self::ensureNullableString($type);

        self::ensureNullableString($source);

        self::ensureNonEmptyString($destination);

        self::ensureNullableString($subpath);

        $readonly = self::ensureBool($readonly === null ? false : $readonly);

        $nocopy = self::ensureBool($nocopy === null ? false : $nocopy);

        $opt = self::ensureStringMap($opt === null ? [] : $opt);

        $this->type = $type;
        $this->source = $source;
        $this->destination = $destination;
        $this->subpath = $subpath;
        $this->readonly = $readonly ?: false;
        $this->nocopy = $nocopy ?: false;
        $this->opt = $opt ?: [];
    }

    public function __toString()
    {
        $parts = [];
        if ($this->type !== null) {
            $parts[] = 'type='.$this->type;
        }
        if ($this->source !== null) {
            $parts[] = 'source='.$this->source;
        }
        $parts[] = 'destination='.$this->destination;
        if ($this->subpath !== null) {
            $parts[] = 'volume-subpath='.$this->subpath;
        }
        if ($this->readonly) {
            $parts[] = 'readonly';
        }
        if ($this->nocopy) {
            $parts[] = 'volume-nocopy';
        }
        foreach ($this->opt as $key => $value) {
            $parts[] = 'volume-opt='.$key.'='.$value;
        }

        return implode(',', $parts);
    }

    /**
     * Get the value of a property.
     *
     * @param string $name the name of the property
     *
     * @return mixed the value of the property
     */
    public function __get($name)
    {
        if (!property_exists($this, $name)) {
            throw new \LogicException('Mount::'.$name.' does not exist');
        }

        return $this->{$name};
    }

    /**
     * Create a Mount object from a string.
     *
     * @param string $v the mount string
     * @psalm-param mixed $v
     *
     * @throws InvalidFormatException if the format is invalid
     *
     * @return Mount the Mount object
     */
    public static function fromString($v)
    {
        self::ensureString($v);

        if (strpos($v, ':') > 0 || strpos($v, '=') === false) {
            return self::fromVolumeString($v);
        }

        return self::fromMountString($v);
    }

    /**
     * Create a Mount object from a volume string.
     *
     * @param string $v the volume string
     * @psalm-param mixed $v
     *
     * @return Mount the Mount object
     */
    public static function fromVolumeString($v)
    {
        self::ensureString($v);

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
     * @param string $v the mount string
     * @psalm-param mixed $v
     *
     * @throws InvalidFormatException if the format is invalid
     *
     * @return Mount the Mount object
     */
    public static function fromMountString($v)
    {
        self::ensureString($v);

        $parts = explode(',', $v);
        $type = 'bind';
        $source = null;
        $destination = null;
        $subpath = null;
        $readonly = false;
        $nocopy = false;
        $opt = [];
        foreach ($parts as $part) {
            $subParts = explode('=', $part, 2);
            if (in_array($subParts[0], ['type', 'source', 'src', 'destination', 'dst', 'target', 'volume-subpath'], true) && !isset($subParts[1])) {
                throw new InvalidFormatException($part, 'key=value');
            }

            switch ($subParts[0]) {
                case 'type':
                    $type = self::ensureString(isset($subParts[1]) ? $subParts[1] : null);

                    break;

                case 'source':
                case 'src':
                    $source = self::ensureString(isset($subParts[1]) ? $subParts[1] : null);

                    break;

                case 'destination':
                case 'dst':
                case 'target':
                    $destination = self::ensureString(isset($subParts[1]) ? $subParts[1] : null);

                    break;

                case 'volume-subpath':
                    $subpath = self::ensureString(isset($subParts[1]) ? $subParts[1] : null);

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
                    throw new \LogicException('unimplemented');

                default:
                    throw new InvalidFormatException($subParts[0], ['type', 'source', 'src', 'destination', 'dst', 'target', 'volume-subpath', 'readonly', 'ro', 'volume-nocopy', 'volume-opt']);
            }
        }

        if ($destination === null) {
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
     * } $v The mount array
     * @psalm-param mixed $v
     *
     * @return Mount the Mount object
     */
    public static function fromArray($v)
    {
        self::ensureArray($v);

        $type = self::ensureNullableString(isset($v['type']) ? $v['type'] : null);

        $source = self::ensureNullableString(isset($v['source']) ? $v['source'] : null);

        if ($source === null) {
            $source = self::ensureNullableString(isset($v['src']) ? $v['src'] : null);

        }
        $destination = self::ensureNullableString(isset($v['destination']) ? $v['destination'] : null);

        if ($destination === null) {
            $destination = self::ensureNullableString(isset($v['dst']) ? $v['dst'] : null);

        }
        if ($destination === null) {
            $destination = self::ensureNullableString(isset($v['target']) ? $v['target'] : null);

        }
        if ($destination === null) {
            throw new \InvalidArgumentException('Invalid mount configuration: destination is required');
        }
        $subpath = self::ensureNullableString(isset($v['subpath']) ? $v['subpath'] : null);

        $readonly = self::ensureBool(isset($v['readonly']) ? $v['readonly'] : false);

        if ($readonly === false) {
            $readonly = self::ensureBool(isset($v['ro']) ? $v['ro'] : false);

        }
        $nocopy = self::ensureBool(isset($v['nocopy']) ? $v['nocopy'] : false);

        $opt = self::ensureStringMap(isset($v['opt']) ? $v['opt'] : []);

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
     * @return null|string the mount type
     */
    public function toString()
    {
        return (string) $this;
    }
}
