<?php

namespace Testcontainers\Containers\Types;

use Testcontainers\Exceptions\InvalidFormatException;
use Testcontainers\Utility\Stringable;

/**
 * Defines the policy for determining when to pull a container image.
 */
class ImagePullPolicy implements Stringable
{
    /**
     * The policy to always pull the image.
     *
     * @var string
     */
    public static $ALWAYS = 'always';

    /**
     * The policy to use when the image is missing.
     *
     * @var string
     */
    public static $MISSING = 'missing';

    /**
     * The policy to never pull the image.
     *
     * @var string
     */
    public static $NEVER = 'never';

    /**
     * The pull policy for the container.
     *
     * @var string
     */
    private $policy;

    /**
     * @param string $policy
     */
    public function __construct($policy)
    {
        assert(in_array($policy, [static::$ALWAYS, static::$MISSING, static::$NEVER]));

        $this->policy = $policy;
    }

    public function __toString()
    {
        return $this->toString();
    }

    /**
     * Create a new instance of `PullPolicy` with the `ALWAYS` policy.
     *
     * @return self
     */
    public static function ALWAYS()
    {
        return new self(static::$ALWAYS);
    }

    /**
     * Create a new instance of `PullPolicy` with the `MISSING` policy.
     *
     * @return self
     */
    public static function MISSING()
    {
        return new self(static::$MISSING);
    }

    /**
     * Create a new instance of `PullPolicy` with the `NEVER` policy.
     *
     * @return self
     */
    public static function NEVER()
    {
        return new self(static::$NEVER);
    }

    /**
     * Create a new instance of `PullPolicy` from a string representation.
     *
     * @param string $policy the string representation of the pull policy
     *
     * @return self
     *
     * @throws InvalidFormatException if the provided policy is not valid
     */
    public static function fromString($policy)
    {
        if (!in_array($policy, [static::$ALWAYS, static::$MISSING, static::$NEVER])) {
            throw new InvalidFormatException($policy, [static::$ALWAYS, static::$MISSING, static::$NEVER]);
        }

        return new self($policy);
    }

    /**
     * Check if the pull policy is set to `ALWAYS`.
     *
     * @return bool true if the policy is `ALWAYS`, false otherwise
     */
    public function isAlways()
    {
        return $this->policy === static::$ALWAYS;
    }

    /**
     * Check if the pull policy is set to `MISSING`.
     *
     * @return bool true if the policy is `MISSING`, false otherwise
     */
    public function isMissing()
    {
        return $this->policy === static::$MISSING;
    }

    /**
     * Check if the pull policy is set to `NEVER`.
     *
     * @return bool true if the policy is `NEVER`, false otherwise
     */
    public function isNever()
    {
        return $this->policy === static::$NEVER;
    }

    /**
     * Get the string representation of the pull policy.
     *
     * @return string the string representation of the pull policy
     */
    public function toString()
    {
        return $this->policy;
    }
}
