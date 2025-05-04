<?php

namespace Testcontainers\Docker\Exception;

use Exception;

/**
 * Exception thrown when a value is invalid.
 *
 * @template T
 */
class InvalidValueException extends \Exception
{
    /**
     * The context of the invalid value.
     *
     * @var null|T
     */
    private $context;

    /**
     * @param string $message
     * @param null|T $context
     */
    public function __construct($message, $context = null)
    {
        parent::__construct($message);
        $this->context = $context;
    }

    /**
     * Get the context of the invalid value.
     *
     * @return null|T
     */
    public function getContext()
    {
        return $this->context;
    }
}
