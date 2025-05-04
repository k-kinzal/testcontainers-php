<?php

namespace Testcontainers\Containers\GenericContainer;

use Testcontainers\Containers\Types\BindMode;
use Testcontainers\Containers\Types\Mount;
use Testcontainers\Exceptions\InvalidFormatException;

/**
 * MountSetting is a trait that provides the ability to add file system bindings to a container.
 *
 * Two formats are supported:
 * 1. static variable `$MOUNTS`:
 *
 * <code>
 * class YourContainer extends GenericContainer
 * {
 *     protected static $MOUNTS = [
 *         'hostPath1:containerPath1:ro',
 *         'type=bind,source=hostPath2,destination=containerPath2,readonly',
 *     ];
 * }
 * </code>
 *
 * 2. method `withFileSystemBind`:
 *
 * <code>
 *     $container = (new YourContainer('image'))
 *        ->withFileSystemBind('hostPath3', 'containerPath3', BindMode::READ_ONLY);
 * </code>
 */
trait MountSetting
{
    /**
     * Define the default mounts to be used for the container.
     *
     * @var null|string[]
     */
    protected static $MOUNTS;

    /**
     * Define the default volumes to be used for the container. (Alias for $MOUNTS).
     *
     * @var null|string[]
     */
    protected static $VOLUMES;

    /**
     * The mounts to be used for the container.
     *
     * @var Mount[]
     */
    private $mounts = [];

    /**
     * Adds a file system binding to the container.
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
     * }|string|Mount $hostPath The path on the host machine. or a string/array/Mount instance representing the mount configuration.
     * @param null|string   $containerPath the path inside the container
     * @param null|BindMode $mode          The mode of the bind (e.g., read-only or read-write).
     *
     * @throws InvalidFormatException if the mount format is invalid
     *
     * @return self
     */
    public function withFileSystemBind($hostPath, $containerPath = null, $mode = null)
    {
        if (null === $containerPath || null === $mode) {
            if (is_string($hostPath)) {
                $mount = Mount::fromString($hostPath);
            } elseif (is_array($hostPath)) {
                $mount = Mount::fromArray($hostPath);
            } elseif ($hostPath instanceof Mount) {
                $mount = $hostPath;
            } else {
                throw new \InvalidArgumentException('Invalid hostPath provided. Expected a string, array, or Mount instance.');
            }
        } elseif (is_string($hostPath)) {
            $mount = Mount::fromArray([
                'type' => 'bind',
                'source' => $hostPath,
                'destination' => $containerPath,
                'readonly' => $mode->isReadOnly(),
            ]);
        } else {
            throw new \InvalidArgumentException('Invalid hostPath provided. Expected a string, array, or Mount instance.');
        }

        $this->mounts[] = $mount;

        return $this;
    }

    /**
     * Adds multiple file system bindings to the container.
     *
     * @param array{
     *      type?: string,
     *      source?: string,
     *      src?: string,
     *      destination?: string,
     *      dst?: string,
     *      target?: string,
     *      subpath?: string,
     *      readonly?: bool,
     *      ro?: bool,
     *      nocopy?: bool,
     *      opt?: array<string, string>
     *  }[]|string[]|Mount[] $mounts An array of mounts, where each mount is a string, array, or Mount instance representing the mount configuration
     *
     * @throws InvalidFormatException if the mount format is invalid
     *
     * @return self
     */
    public function withFileSystemBinds($mounts)
    {
        $this->mounts = [];
        foreach ($mounts as $mount) {
            $this->withFileSystemBind($mount);
        }

        return $this;
    }

    /**
     * Adds a file system binding to the container. (Alias for withFileSystemBind).
     *
     * @param array{
     *      type?: string,
     *      source?: string,
     *      src?: string,
     *      destination?: string,
     *      dst?: string,
     *      target?: string,
     *      subpath?: string,
     *      readonly?: bool,
     *      ro?: bool,
     *      nocopy?: bool,
     *      opt?: array<string, string>
     *  }|string|Mount $hostPath The path on the host machine. or a string/array/Mount instance representing the mount configuration.
     * @param null|string   $containerPath the path inside the container
     * @param null|BindMode $mode          The mode of the bind (e.g., read-only or read-write).
     *
     * @throws InvalidFormatException if the mount format is invalid
     *
     * @return self
     */
    public function withVolume($hostPath, $containerPath = null, $mode = null)
    {
        return $this->withFileSystemBind($hostPath, $containerPath, $mode);
    }

    /**
     * Adds multiple file system bindings to the container. (Alias for withFileSystemBinds).
     *
     * @param array{
     *      type?: string,
     *      source?: string,
     *      src?: string,
     *      destination?: string,
     *      dst?: string,
     *      target?: string,
     *      subpath?: string,
     *      readonly?: bool,
     *      ro?: bool,
     *      nocopy?: bool,
     *      opt?: array<string, string>
     *  }[]|string[]|Mount[] $mounts An array of mounts, where each mount is a string, array, or Mount instance representing the mount configuration
     *
     * @throws InvalidFormatException if the mount format is invalid
     *
     * @return self
     */
    public function withVolumes($mounts)
    {
        return $this->withFileSystemBinds($mounts);
    }

    /**
     * Adds a file system binding to the container. (Alias for withFileSystemBind).
     *
     * @param array{
     *      type?: string,
     *      source?: string,
     *      src?: string,
     *      destination?: string,
     *      dst?: string,
     *      target?: string,
     *      subpath?: string,
     *      readonly?: bool,
     *      ro?: bool,
     *      nocopy?: bool,
     *      opt?: array<string, string>
     *  }|string|Mount $hostPath The path on the host machine. or a string/array/Mount instance representing the mount configuration.
     * @param null|string   $containerPath the path inside the container
     * @param null|BindMode $mode          The mode of the bind (e.g., read-only or read-write).
     *
     * @throws InvalidFormatException if the mount format is invalid
     *
     * @return self
     */
    public function withMount($hostPath, $containerPath = null, $mode = null)
    {
        return $this->withFileSystemBind($hostPath, $containerPath, $mode);
    }

    /**
     * Adds multiple file system bindings to the container. (Alias for withFileSystemBinds).
     *
     * @param array{
     *      type?: string,
     *      source?: string,
     *      src?: string,
     *      destination?: string,
     *      dst?: string,
     *      target?: string,
     *      subpath?: string,
     *      readonly?: bool,
     *      ro?: bool,
     *      nocopy?: bool,
     *      opt?: array<string, string>
     *  }[]|string[]|Mount[] $mounts An array of mounts, where each mount is a string, array, or Mount instance representing the mount configuration
     *
     * @throws InvalidFormatException if the mount format is invalid
     *
     * @return self
     */
    public function withMounts($mounts)
    {
        return $this->withFileSystemBinds($mounts);
    }

    /**
     * Retrieve the mounts to be used for the container.
     *
     * This method returns an array of mounts, where each mount is an associative array
     * containing the host path, container path, and bind mode.
     *
     * @throws InvalidFormatException if the mount format is invalid
     *
     * @return Mount[] the mounts to be used for the container
     */
    protected function mounts()
    {
        $mounts = static::$MOUNTS;
        if (empty($mounts)) {
            $mounts = static::$VOLUMES;
        }
        if (!empty($mounts)) {
            $m = [];
            foreach ($mounts as $mount) {
                $m[] = Mount::fromString($mount);
            }

            return $m;
        }

        return $this->mounts;
    }
}
