<?php

namespace Testcontainers\Containers\PortStrategy;

use Testcontainers\Exceptions\InvalidFormatException;

/**
 * Represents the behavior to take when a port conflict occurs.
 *
 * This class defines the actions that can be taken when a port conflict is detected,
 * such as retrying the operation or failing immediately.
 */
class ConflictBehavior
{
    /**
     * Retry action for conflict behavior.
     *
     * This constant represents the action to retry when a port conflict occurs.
     *
     * @var string
     */
    public static $RETRY = 'retry';

    /**
     * Fail action for conflict behavior.
     *
     * This constant represents the action to fail when a port conflict occurs.
     *
     * @var string
     */
    public static $FAIL = 'fail';

    /**
     * The action to take when a port conflict occurs.
     *
     * This property holds the action to be taken, which can be either `retry` or `fail`.
     *
     * @var string
     */
    private $action;

    /**
     * @param string $action
     */
    public function __construct($action)
    {
        assert(in_array($action, [static::$RETRY, static::$FAIL]));

        $this->action = $action;
    }

    /**
     * Creates a ConflictBehavior instance with the retry action.
     *
     * This method returns a new ConflictBehavior instance configured to retry
     * when a port conflict occurs.
     *
     * @return self A ConflictBehavior instance with the retry action.
     */
    public static function RETRY()
    {
        return new self(static::$RETRY);
    }

    /**
     * Creates a ConflictBehavior instance with the fail action.
     *
     * This method returns a new ConflictBehavior instance configured to fail
     * when a port conflict occurs.
     *
     * @return self A ConflictBehavior instance with the fail action.
     */
    public static function FAIL()
    {
        return new self(static::$FAIL);
    }

    /**
     * Creates a ConflictBehavior instance from a string.
     *
     * @param string $action The action to take on port conflict. Valid values are 'retry' or 'fail'.
     * @return ConflictBehavior The ConflictBehavior instance corresponding to the action.
     *
     * @throws InvalidFormatException If the action is invalid.
     */
    public static function fromString($action)
    {
        if (!in_array($action, [static::$RETRY, static::$FAIL])) {
            throw new InvalidFormatException($action, [static::$RETRY, static::$FAIL]);
        }
        return new self($action);
    }

    /**
     * Checks if the conflict behavior is set to retry.
     *
     * @return bool True if the action is retry, false otherwise.
     */
    public function isRetry()
    {
        return $this->action === static::$RETRY;
    }

    /**
     * Checks if the conflict behavior is set to fail.
     *
     * @return bool True if the action is fail, false otherwise.
     */
    public function isFail()
    {
        return $this->action === static::$FAIL;
    }

    /**
     * Converts the conflict behavior to a string representation.
     *
     * @return string The string representation of the conflict behavior.
     */
    public function toString()
    {
        return $this->action;
    }

    public function __toString()
    {
        return $this->toString();
    }
}
