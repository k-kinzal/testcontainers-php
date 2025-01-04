<?php

namespace Testcontainers\Docker\Types;

use LogicException;
use Testcontainers\Docker\Exception\InvalidValueException;

/**
 * Represents a container object returned by the Docker API.
 *
 * @property-read State $state The state of the container.
 */
class ContainerObject
{
    /**
     * The state of the container.
     * @var State
     */
    private $state;


    private function __construct()
    {
    }

    /**
     * Create a ContainerObject from an array.
     *
     * @param array $arr The array to create the ContainerObject from.
     * @return self
     *
     * @throws InvalidValueException If the array does not contain the expected properties.
     */
    public static function fromArray($arr)
    {
        $object = new ContainerObject();
        $object->state = self::ensureStateFromArray($arr);
        return $object;
    }

    /**
     * Ensure that the State property is present and is an array.
     *
     * @param array $arr The array to check.
     * @return State The state of the container.
     *
     * @throws InvalidValueException If the State property is missing or is not an array.
     */
    private static function ensureStateFromArray($arr)
    {
        if (!isset($arr['State'])) {
            throw new InvalidValueException("ContainerObject expects an array 'State' property, but 'State' is missing", ['data' => $arr]);
        }
        if (!is_array($arr['State'])) {
            throw new InvalidValueException(
                sprintf(
                    "ContainerObject expects an array 'State' property, but received a value `%s`",
                    var_export($arr['State'], true)
                ),
                ['data' => $arr]
            );
        }
        return State::fromArray($arr['State']);
    }

    /**
     * Retrieve the value of a property.
     *
     * @param string $name The name of the property to retrieve.
     * @return mixed
     */
    public function __get($name)
    {
        if (!property_exists($this, $name)) {
            throw new LogicException('ContainerObject::' . $name . ' does not exist');
        }
        return $this->$name;
    }
}