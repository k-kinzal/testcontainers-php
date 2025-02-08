<?php

namespace Testcontainers\Containers\GenericContainer;

use InvalidArgumentException;
use Testcontainers\Containers\BindMode;
use Testcontainers\Containers\ContainerInstance;
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
     * @var string[]|null
     */
    protected static $VOLUMES_FROM;

    /**
     * The volumes to be used for the container.
     * @var array{
     *    name: string,
     *    mode: BindMode,
     * }[]
     */
    private $volumesFrom = [];

    /**
     * Adds container volumes to the current container instance.
     *
     * @param ContainerInstance $container The container instance from which to add volumes.
     * @param BindMode $mode The mode of the bind (e.g., read-only or read-write).
     * @return self
     */
    public function withVolumesFrom($container, $mode)
    {
        $this->volumesFrom[] = [
            'name' => $container->getContainerId(),
            'mode' => $mode,
        ];

        return $this;
    }

    /**
     * Retrieve the volumes to be used for the container.
     *
     * This method returns an array of volumes, where each volume is an associative array
     * containing the container name and bind mode.
     *
     * @return array{
     *     name: string,
     *     mode: BindMode,
     * }[] The volumes to be used for the container.
     *
     * @throws InvalidFormatException If the volume format is invalid.
     */
    protected function volumesFrom()
    {
        $targets = static::$VOLUMES_FROM;
        if ($targets === null) {
            $targets = $this->volumesFrom;
        }

        $volumesFrom = [];
        foreach ($targets as $volume) {
            if (is_string($volume)) {
                $parts = explode(':', $volume);
                $volume = [
                    'name' => $parts[0],
                    'mode' => isset($parts[1]) ? BindMode::fromString($parts[1]) : BindMode::READ_WRITE(),
                ];
            }

            if (!isset($volume['name'])) {
                throw new InvalidArgumentException('Missing container name in volumes from');
            }
            if (!isset($volume['mode'])) {
                throw new InvalidArgumentException('Missing bind mode in volumes from');
            }

            $volumesFrom[] = $volume;
        }

        return empty($volumesFrom) ? null : $volumesFrom;
    }
}
