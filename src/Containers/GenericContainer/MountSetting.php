<?php

namespace Testcontainers\Containers\GenericContainer;

use Testcontainers\Containers\BindMode;
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
     * @var string[]|null
     */
    protected static $MOUNTS;

    /**
     * The mounts to be used for the container.
     * @var Mount[]
     */
    private $mounts = [];

    /**
     * Adds a file system binding to the container.
     *
     * @param string $hostPath The path on the host machine.
     * @param string $containerPath The path inside the container.
     * @param BindMode $mode The mode of the bind (e.g., read-only or read-write).
     * @return self
     */
    public function withFileSystemBind($hostPath, $containerPath, $mode)
    {
        $this->mounts[] = Mount::fromArray([
            'type' => 'bind',
            'source' => $hostPath,
            'destination' => $containerPath,
            'readonly' => $mode->isReadOnly(),
        ]);

        return $this;
    }

    /**
     * Retrieve the mounts to be used for the container.
     *
     * This method returns an array of mounts, where each mount is an associative array
     * containing the host path, container path, and bind mode.
     *
     * @return Mount[] The mounts to be used for the container.
     *
     * @throws InvalidFormatException If the mount format is invalid.
     */
    protected function mounts()
    {
        if (!empty(static::$MOUNTS)) {
            $mounts = [];
            foreach (static::$MOUNTS as $mount) {
                $mounts[] = Mount::fromString($mount);
            }
            return $mounts;
        }
        return $this->mounts;
    }
}