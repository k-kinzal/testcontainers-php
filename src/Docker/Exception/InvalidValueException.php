<?php

namespace Testcontainers\Docker\Exception;

use Exception;

/**
 * Exception thrown when a value is invalid.
 *
 * @template T
 */
class InvalidValueException extends Exception
{
    /**
     * The context of the invalid value.
     *
     * @var T|null
     */
    private $context;

    /**
     * @param string $message
     * @param T|null $context
     */
    public function __construct($message, $context = null)
    {
        parent::__construct($message);
        $this->context = $context;
    }

    /**
     * Get the context of the invalid value.
     *
     * @return T|null
     */
    public function getContext()
    {
        return $this->context;
    }
}
