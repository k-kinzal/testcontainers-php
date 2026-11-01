<?php

namespace Testcontainers\Docker\Types;

use Testcontainers\Utility\Ensure;
use Testcontainers\Docker\Exception\InvalidValueException;
use Testcontainers\Exceptions\InvalidFormatException;

/**
 * Represents an event emitted by the Docker daemon.
 *
 * @property EventType       $type     The type of the object that generated the event
 * @property EventAction     $action   The action that the event represents
 * @property EventActor      $actor    The object that generated the event
 * @property null|EventScope $scope    The scope of the event
 * @property null|int        $time     The time of the event in seconds
 * @property null|int        $timeNano The time of the event in nanoseconds
 * @property null|string     $status   Deprecated status of the event
 * @property null|string     $id       Deprecated ID of the object that generated the event
 * @property null|string     $from     Deprecated image of the container that generated the event
 * @api
 */
class Event
{
    use Ensure;

    /**
     * The type of the object that generated the event.
     *
     * @var EventType
     */
    private $type;

    /**
     * The action that the event represents.
     *
     * @var EventAction
     */
    private $action;

    /**
     * The object that generated the event.
     *
     * @var EventActor
     */
    private $actor;

    /**
     * The scope of the event.
     *
     * @var null|EventScope
     */
    private $scope;

    /**
     * The time of the event in seconds since the Unix epoch.
     *
     * @var null|int
     */
    private $time;

    /**
     * The time of the event in nanoseconds since the Unix epoch.
     *
     * @var null|int
     */
    private $timeNano;

    /**
     * Deprecated status of the event.
     *
     * @var null|string
     */
    private $status;

    /**
     * Deprecated ID of the object that generated the event.
     *
     * @var null|string
     */
    private $id;

    /**
     * Deprecated image of the container that generated the event.
     *
     * @var null|string
     */
    private $from;

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
            throw new \LogicException('Event::'.$name.' does not exist');
        }

        return $this->{$name};
    }

    /**
     * Create an Event from a JSON-decoded array.
     *
     * @param array $arr the decoded JSON object of a Docker event
     * @psalm-param mixed $arr
     *
     * @throws InvalidValueException if the array does not contain the expected properties
     *
     * @return self
     */
    public static function fromArray($arr)
    {
        self::ensureArray($arr);

        $event = new self();
        $event->type = self::ensureTypeFromArray($arr);

        $event->action = self::ensureActionFromArray($arr);

        $event->actor = self::ensureActorFromArray($arr);

        $event->scope = self::ensureScopeFromArray($arr);

        $event->time = self::ensureOptionalIntFromArray($arr, 'time');

        $event->timeNano = self::ensureOptionalIntFromArray($arr, 'timeNano');

        $event->status = self::ensureOptionalStringFromArray($arr, 'status');

        $event->id = self::ensureOptionalStringFromArray($arr, 'id');

        $event->from = self::ensureOptionalStringFromArray($arr, 'from');

        return $event;
    }

    /**
     * Deserialize an Event from a JSON string.
     *
     * @param string $s the JSON representation of a Docker event
     * @psalm-param mixed $s
     *
     * @throws InvalidValueException if the string is not a valid event
     *
     * @return self
     */
    public static function deserialize($s)
    {
        self::ensureString($s);

        $decoded = json_decode($s, true);
        if (!is_array($decoded)) {
            throw new InvalidValueException(sprintf('Event expects a JSON object, but received `%s`', $s), ['data' => $s]);
        }

        return self::fromArray($decoded);
    }

    /**
     * Ensure that the 'Type' property is one of the known event types.
     *
     * @param array $arr the array to check
     *
     * @throws InvalidValueException if the 'Type' property is missing, not a string, or not a known event type
     *
     * @return EventType
     */
    private static function ensureTypeFromArray($arr)
    {
        $type = self::ensureStringFromArray($arr, 'Type');

        try {
            return EventType::fromString($type);
        } catch (InvalidFormatException $e) {
            throw new InvalidValueException(
                sprintf("Event expects a 'Type' property to be one of [%s], but received `%s`", implode(', ', EventType::values()), $type),
                ['data' => $arr]
            );
        }
    }

    /**
     * Ensure that the 'Action' property is one of the known event actions.
     *
     * @param array $arr the array to check
     *
     * @throws InvalidValueException if the 'Action' property is missing, not a string, or not a known event action
     *
     * @return EventAction
     */
    private static function ensureActionFromArray($arr)
    {
        $action = self::ensureStringFromArray($arr, 'Action');

        try {
            return EventAction::fromString($action);
        } catch (InvalidFormatException $e) {
            throw new InvalidValueException(
                sprintf("Event expects an 'Action' property to be one of [%s], but received `%s`", implode(', ', EventAction::values()), $action),
                ['data' => $arr]
            );
        }
    }

    /**
     * Ensure that the 'Actor' property is present and is an array.
     *
     * @param array $arr the array to check
     *
     * @throws InvalidValueException if the 'Actor' property is missing or invalid
     *
     * @return EventActor
     */
    private static function ensureActorFromArray($arr)
    {
        if (!isset($arr['Actor'])) {
            throw new InvalidValueException("Event expects an array 'Actor' property, but 'Actor' is missing", ['data' => $arr]);
        }
        if (!is_array($arr['Actor'])) {
            throw new InvalidValueException(
                sprintf(
                    "Event expects an array 'Actor' property, but received a value `%s`",
                    var_export($arr['Actor'], true)
                ),
                ['data' => $arr]
            );
        }

        return EventActor::fromArray($arr['Actor']);
    }

    /**
     * Ensure that the optional 'scope' property is one of the known event scopes.
     *
     * @param array $arr the array to check
     *
     * @throws InvalidValueException if the 'scope' property is not a known event scope
     *
     * @return null|EventScope
     */
    private static function ensureScopeFromArray($arr)
    {
        $scope = self::ensureOptionalStringFromArray($arr, 'scope');

        if ($scope === null) {
            return null;
        }

        try {
            return EventScope::fromString($scope);
        } catch (InvalidFormatException $e) {
            throw new InvalidValueException(
                sprintf("Event expects a 'scope' property to be one of [%s], but received `%s`", implode(', ', EventScope::values()), $scope),
                ['data' => $arr]
            );
        }
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
                sprintf("Event expects a string '%s' property, but '%s' is missing", $name, $name),
                ['data' => $arr]
            );
        }
        if (!is_string($arr[$name])) {
            throw new InvalidValueException(
                sprintf(
                    "Event expects a string '%s' property, but received a value `%s`",
                    $name,
                    var_export($arr[$name], true)
                ),
                ['data' => $arr]
            );
        }

        return $arr[$name];
    }

    /**
     * Ensure that an optional string property is a string if present.
     *
     * @param array  $arr  the array to check
     * @param string $name the property name
     *
     * @throws InvalidValueException if the property is present but not a string
     *
     * @return null|string
     */
    private static function ensureOptionalStringFromArray($arr, $name)
    {
        if (!isset($arr[$name])) {
            return null;
        }
        if (!is_string($arr[$name])) {
            throw new InvalidValueException(
                sprintf(
                    "Event expects a string '%s' property, but received a value `%s`",
                    $name,
                    var_export($arr[$name], true)
                ),
                ['data' => $arr]
            );
        }

        return $arr[$name];
    }

    /**
     * Ensure that an optional integer property is an integer if present.
     *
     * @param array  $arr  the array to check
     * @param string $name the property name
     *
     * @throws InvalidValueException if the property is present but not an integer
     *
     * @return null|int
     */
    private static function ensureOptionalIntFromArray($arr, $name)
    {
        if (!isset($arr[$name])) {
            return null;
        }
        if (!is_int($arr[$name])) {
            throw new InvalidValueException(
                sprintf(
                    "Event expects an integer '%s' property, but received a value `%s`",
                    $name,
                    var_export($arr[$name], true)
                ),
                ['data' => $arr]
            );
        }

        return $arr[$name];
    }
}
