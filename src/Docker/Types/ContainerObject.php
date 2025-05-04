<?php

namespace Testcontainers\Docker\Types;

use Testcontainers\Docker\Exception\InvalidValueException;

/**
 * Represents a container object returned by the Docker API.
 *
 * @property State $state The state of the container.
 */
class ContainerObject
{
    /**
     * The state of the container.
     *
     * @var State
     */
    private $state;

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
        if (!property_exists($this, $name)) {
            throw new \LogicException('ContainerObject::'.$name.' does not exist');
        }

        return $this->{$name};
    }

    /**
     * Create a ContainerObject from an array.
     *
     * @param array $arr the array to create the ContainerObject from
     *
     * @throws InvalidValueException if the array does not contain the expected properties
     *
     * @return self
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
     * @param array $arr the array to check
     *
     * @throws InvalidValueException if the State property is missing or is not an array
     *
     * @return State the state of the container
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
}
