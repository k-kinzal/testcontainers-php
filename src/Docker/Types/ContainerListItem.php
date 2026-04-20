<?php

namespace Testcontainers\Docker\Types;

use Testcontainers\Docker\Exception\InvalidValueException;

use function Testcontainers\ensure;

/**
 * Represents a container entry from the `docker ps` command output.
 *
 * This class is a value object that holds the parsed data from a single
 * container listing in JSON format.
 *
 * @property ContainerId                 $id           The container ID
 * @property string                      $command      The command running in the container
 * @property string                      $createdAt    The creation timestamp
 * @property string                      $image        The image used to create the container
 * @property array<string, string>       $labels       The container labels as key-value pairs
 * @property int                         $localVolumes The number of local volumes
 * @property string[]                    $mounts       The mount points
 * @property string                      $names        The container name
 * @property string[]                    $networks     The networks the container is connected to
 * @property string                      $ports        The port mappings
 * @property string                      $runningFor   The human-readable duration since creation
 * @property string                      $size         The container size
 * @property string                      $state        The container state (created, running, paused, restarting, removing, exited, dead)
 * @property string                      $status       The human-readable status string
 */
class ContainerListItem
{
    /**
     * @var ContainerId
     */
    private $id;

    /**
     * @var string
     */
    private $command;

    /**
     * @var string
     */
    private $createdAt;

    /**
     * @var string
     */
    private $image;

    /**
     * @var array<string, string>
     */
    private $labels;

    /**
     * @var int
     */
    private $localVolumes;

    /**
     * @var string[]
     */
    private $mounts;

    /**
     * @var string
     */
    private $names;

    /**
     * @var string[]
     */
    private $networks;

    /**
     * @var string
     */
    private $ports;

    /**
     * @var string
     */
    private $runningFor;

    /**
     * @var string
     */
    private $size;

    /**
     * @var string
     */
    private $state;

    /**
     * @var string
     */
    private $status;

    private function __construct()
    {
    }

    /**
     * Retrieve the value of a property.
     *
     * @param string $name the name of the property to retrieve
     *
     * @return mixed
     */
    public function __get($name)
    {
        ensure(is_string($name), '$name must be string');

        if (!property_exists($this, $name)) {
            throw new \LogicException('ContainerListItem::'.$name.' does not exist');
        }

        return $this->{$name};
    }

    /**
     * Create a ContainerListItem from a JSON-decoded array.
     *
     * @param array $arr the decoded JSON array from `docker ps --format json`
     *
     * @throws InvalidValueException if the array does not contain the expected properties
     *
     * @return self
     */
    public static function fromArray($arr)
    {
        ensure(is_array($arr), '$arr must be array');

        $item = new self();
        $item->id = self::ensureIdFromArray($arr);
        $item->command = self::ensureStringFromArray($arr, 'Command');
        $item->createdAt = self::ensureStringFromArray($arr, 'CreatedAt');
        $item->image = self::ensureStringFromArray($arr, 'Image');
        $item->labels = self::parseLabels(self::ensureStringFromArray($arr, 'Labels'));
        $item->localVolumes = (int) self::ensureStringFromArray($arr, 'LocalVolumes');
        $item->mounts = self::parseCsvString(self::ensureStringFromArray($arr, 'Mounts'));
        $item->names = self::ensureStringFromArray($arr, 'Names');
        $item->networks = self::parseCsvString(self::ensureStringFromArray($arr, 'Networks'));
        $item->ports = self::ensureStringFromArray($arr, 'Ports');
        $item->runningFor = self::ensureStringFromArray($arr, 'RunningFor');
        $item->size = isset($arr['Size']) && is_string($arr['Size']) ? $arr['Size'] : '';
        $item->state = self::ensureStringFromArray($arr, 'State');
        $item->status = self::ensureStringFromArray($arr, 'Status');

        return $item;
    }

    /**
     * Get a label value by key.
     *
     * @param string $key the label key
     *
     * @return null|string the label value, or null if not found
     */
    public function getLabel($key)
    {
        ensure(is_string($key), '$key must be string');

        return isset($this->labels[$key]) ? $this->labels[$key] : null;
    }

    /**
     * Ensure that the ID property is present and create a ContainerId.
     *
     * @param array $arr the array to check
     *
     * @throws InvalidValueException if the ID property is missing or invalid
     *
     * @return ContainerId
     */
    private static function ensureIdFromArray($arr)
    {
        if (!isset($arr['ID'])) {
            throw new InvalidValueException("ContainerListItem expects a string 'ID' property, but 'ID' is missing", ['data' => $arr]);
        }
        if (!is_string($arr['ID'])) {
            throw new InvalidValueException(
                sprintf(
                    "ContainerListItem expects a string 'ID' property, but received a value `%s`",
                    var_export($arr['ID'], true)
                ),
                ['data' => $arr]
            );
        }

        return new ContainerId($arr['ID']);
    }

    /**
     * Ensure that a string property is present in the array.
     *
     * @param array  $arr  the array to check
     * @param string $name the property name
     *
     * @throws InvalidValueException if the property is missing or not a string
     *
     * @return string
     */
    private static function ensureStringFromArray($arr, $name)
    {
        if (!isset($arr[$name])) {
            throw new InvalidValueException(
                sprintf("ContainerListItem expects a string '%s' property, but '%s' is missing", $name, $name),
                ['data' => $arr]
            );
        }
        if (!is_string($arr[$name])) {
            throw new InvalidValueException(
                sprintf(
                    "ContainerListItem expects a string '%s' property, but received a value `%s`",
                    $name,
                    var_export($arr[$name], true)
                ),
                ['data' => $arr]
            );
        }

        return $arr[$name];
    }

    /**
     * Parse a Docker labels string into an associative array.
     *
     * @param string $labelsStr comma-separated key=value pairs
     *
     * @return array<string, string>
     */
    private static function parseLabels($labelsStr)
    {
        $labels = [];
        if ($labelsStr === '') {
            return $labels;
        }

        foreach (explode(',', $labelsStr) as $labelPair) {
            $kv = explode('=', $labelPair, 2);
            if (count($kv) === 2) {
                $labels[trim($kv[0])] = trim($kv[1]);
            }
        }

        return $labels;
    }

    /**
     * Parse a comma-separated string into an array of trimmed values.
     *
     * @param string $str comma-separated values
     *
     * @return string[]
     */
    private static function parseCsvString($str)
    {
        if ($str === '') {
            return [];
        }

        return array_map('trim', explode(',', $str));
    }
}
