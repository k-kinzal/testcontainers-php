<?php

namespace Testcontainers\Docker\Types;

use Testcontainers\Utility\Ensure;
use Testcontainers\Docker\Exception\InvalidValueException;

/**
 * Represents the object that generated a Docker event.
 *
 * @property string                $id         The ID of the object that generated the event
 * @property array<string, string> $attributes The attributes of the object that generated the event
 */
class EventActor
{
    use Ensure;

    /**
     * The ID of the object that generated the event.
     *
     * @var string
     */
    private $id;

    /**
     * The attributes of the object that generated the event.
     *
     * @var array<string, string>
     */
    private $attributes;

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
            throw new \LogicException('EventActor::'.$name.' does not exist');
        }

        return $this->{$name};
    }

    /**
     * Create an EventActor from a JSON-decoded array.
     *
     * @param array $arr the decoded `Actor` object of a Docker event
     * @psalm-param mixed $arr
     *
     * @throws InvalidValueException if the array does not contain the expected properties
     *
     * @return self
     */
    public static function fromArray($arr)
    {
        self::ensureArray($arr);

        $actor = new self();
        $actor->id = self::ensureIdFromArray($arr);

        $actor->attributes = self::ensureAttributesFromArray($arr);

        return $actor;
    }

    /**
     * Get an attribute value by key.
     *
     * @param string $key the attribute key
     * @psalm-param mixed $key
     *
     * @return null|string the attribute value, or null if not found
     */
    public function getAttribute($key)
    {
        self::ensureString($key);

        return isset($this->attributes[$key]) ? $this->attributes[$key] : null;
    }

    /**
     * Ensure that the 'ID' property is a string.
     *
     * @param array $arr the array to check
     *
     * @throws InvalidValueException if the 'ID' property is missing or not a string
     *
     * @return string
     */
    private static function ensureIdFromArray($arr)
    {
        if (!isset($arr['ID'])) {
            throw new InvalidValueException("EventActor expects a string 'ID' property, but 'ID' is missing", ['data' => $arr]);
        }
        if (!is_string($arr['ID'])) {
            throw new InvalidValueException(
                sprintf(
                    "EventActor expects a string 'ID' property, but received a value `%s`",
                    var_export($arr['ID'], true)
                ),
                ['data' => $arr]
            );
        }

        return $arr['ID'];
    }

    /**
     * Ensure that the 'Attributes' property is a map of strings, treating a missing or null value as empty.
     *
     * @param array $arr the array to check
     *
     * @throws InvalidValueException if the 'Attributes' property is not a map of strings
     *
     * @return array<string, string>
     */
    private static function ensureAttributesFromArray($arr)
    {
        if (!isset($arr['Attributes'])) {
            return [];
        }
        if (!is_array($arr['Attributes'])) {
            throw new InvalidValueException(
                sprintf(
                    "EventActor expects an array 'Attributes' property, but received a value `%s`",
                    var_export($arr['Attributes'], true)
                ),
                ['data' => $arr]
            );
        }
        $attributes = [];
        foreach ($arr['Attributes'] as $key => $value) {
            if (!is_string($value)) {
                throw new InvalidValueException(
                    sprintf(
                        "EventActor expects a string value for the attribute '%s', but received a value `%s`",
                        $key,
                        var_export($value, true)
                    ),
                    ['data' => $arr]
                );
            }
            $attributes[(string) $key] = $value;
        }

        return $attributes;
    }
}
