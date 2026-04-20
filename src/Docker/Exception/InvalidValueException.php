<?php

namespace Testcontainers\Docker\Exception;

use Exception;

use function Testcontainers\ensure;

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
        ensure(is_string($message), '$message must be string');

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
