<?php

namespace Testcontainers\Containers\GenericContainer;

use Testcontainers\Containers\ContainerInstance;
use Testcontainers\Containers\Types\BindMode;
use Testcontainers\Containers\Types\VolumeFrom;
use Testcontainers\Exceptions\InvalidFormatException;

/**
 * VolumesFromSetting is a trait that provides the ability to add volumes from other containers to a container.
 *
 * Two formats are supported:
 * 1. static variable `$VOLUMES_FROM`:
 *
 * <code>
 * class YourContainer extends GenericContainer
 * {
 *     protected static $VOLUMES_FROM = [
 *         'container1',
 *         'container2:ro',
 *     ];
 * }
 * </code>
 *
 * 2. method `withVolumesFrom`:
 *
 * <code>
 *     $container = (new YourContainer('image'))
 *        ->withVolumesFrom($container1, BindMode::READ_ONLY);
 * </code>
 */
trait VolumesFromSetting
{
    /**
     * Define the default volumes to be used for the container.
     *
     * @var null|array{name: string, mode?: string}[]|string[]
     */
    protected static $VOLUMES_FROM;

    /**
     * The volumes to be used for the container.
     *
     * @var VolumeFrom[]
     */
    private $volumesFrom = [];

    /**
     * Adds container volumes to the current container instance.
     *
     * @param ContainerInstance $container the container instance from which to add volumes
     * @param BindMode          $mode      The mode of the bind (e.g., read-only or read-write).
     *
     * @return self
     */
    public function withVolumesFrom($container, $mode)
    {
        $this->volumesFrom[] = new VolumeFrom((string) $container->getContainerId(), $mode);

        return $this;
    }

    /**
     * Retrieve the volumes to be used for the container.
     *
     * This method returns an array of volumes, where each volume is an associative array
     * containing the container name and bind mode.
     *
     * @throws InvalidFormatException if the volume format is invalid
     *
     * @return VolumeFrom[] the volumes to be used for the container
     */
    protected function volumesFrom()
    {
        $targets = static::$VOLUMES_FROM;
        if (!empty($targets)) {
            $volumesFrom = [];
            foreach ($targets as $volume) {
                if (is_string($volume)) {
                    $volumesFrom[] = VolumeFrom::fromString($volume);
                } elseif (is_array($volume)) {
                    if (!isset($volume['mode'])) {
                        $volume['mode'] = BindMode::READ_WRITE();
                    }
                    if (is_string($volume['mode'])) {
                        $volume['mode'] = BindMode::fromString($volume['mode']);
                    }
                    $volumesFrom[] = VolumeFrom::fromArray($volume);
                } else {
                    throw new InvalidFormatException($volume, 'string|array{name: string, mode?: string}');
                }
            }

            return $volumesFrom;
        }

        return $this->volumesFrom;
    }
}
