<?php

namespace Testcontainers\Docker\Types;

use Testcontainers\Docker\Exception\InvalidValueException;

/**
 * Represents the state of a container.
 *
 * @property string $status   Container status of "created", "running", "paused", "restarting", "removing", "exited", or "dead"
 * @property int    $exitCode The last exit code of the container
 */
class State
{
    /**
     * Container status of "created", "running", "paused", "restarting", "removing", "exited", or "dead".
     *
     * @var string
     */
    private $status;

    /**
     * The last exit code of the container.
     *
     * @var int
     */
    private $exitCode;

    private function __construct() {}

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
            throw new \LogicException('State::'.$name.' does not exist');
        }

        return $this->{$name};
    }

    /**
     * Create a State object from an array.
     *
     * @param array $arr the array to create the State object from
     *
     * @return State
     *
     * @throws InvalidValueException if the array does not contain the expected properties
     */
    public static function fromArray($arr)
    {
        $state = new State();
        $state->status = self::ensureStatusFromArray($arr);
        $state->exitCode = self::ensureExitCodeFromArray($arr);

        return $state;
    }

    /**
     * Ensure that the 'Status' property is a string and is one of the expected values.
     *
     * @param array $arr the array to check
     *
     * @return string
     *
     * @throws InvalidValueException if the 'Status' property is missing, not a string, or not one of the expected values
     */
    public static function ensureStatusFromArray($arr)
    {
        if (!isset($arr['Status'])) {
            throw new InvalidValueException("State expects a string 'Status' property, but 'Status' is missing", ['data' => $arr]);
        }
        if (!is_string($arr['Status'])) {
            throw new InvalidValueException(
                sprintf(
                    "State expects a string 'Status' property, but received a value `%s`",
                    var_export($arr['Status'], true)
                ),
                ['data' => $arr]
            );
        }
        if (!in_array($arr['Status'], ['created', 'running', 'paused', 'restarting', 'removing', 'exited', 'dead'])) {
            throw new InvalidValueException(
                sprintf(
                    "State expects a string 'Status' property to be one of [created, running, paused, restarting, removing, exited, dead], but received `%s`",
                    var_export($arr['Status'], true)
                ),
                ['data' => $arr]
            );
        }

        return $arr['Status'];
    }

    /**
     * Ensure that the 'ExitCode' property is an integer.
     *
     * @param array $arr the array to check
     *
     * @return int
     *
     * @throws InvalidValueException if the 'ExitCode' property is missing or not an integer
     */
    public static function ensureExitCodeFromArray($arr)
    {
        if (!isset($arr['ExitCode'])) {
            throw new InvalidValueException("State expects a integer 'ExitCode' property, but 'ExitCode' is missing", ['data' => $arr]);
        }
        if (!is_int($arr['ExitCode'])) {
            throw new InvalidValueException(
                sprintf(
                    "State expects a integer 'ExitCode' property, but received a value `%s`",
                    var_export($arr['ExitCode'], true)
                ),
                ['data' => $arr]
            );
        }

        return $arr['ExitCode'];
    }
}
