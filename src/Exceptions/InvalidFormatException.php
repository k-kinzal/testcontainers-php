<?php

namespace Testcontainers\Exceptions;

use Exception;

/**
 * Exception thrown when an invalid format is encountered.
 *
 * @package Testcontainers\Containers
 */
class InvalidFormatException extends Exception
{
    /**
         * InvalidFormatException constructor.
         *
         * @param string $actual The actual format encountered.
         * @param string[] $expects An array of expected formats.
         * @param int $code The exception code.
         * @param Exception|null $previous The previous exception used for exception chaining.
         */
    public function __construct($actual, $expects = [], $code = 0, $previous = null)
    {
        if (empty($expects)) {
            $message = "Invalid format: $actual";
            parent::__construct("Invalid format: $actual", $code, $previous);
        } else {
            $expects = implode(', ', $expects);
            parent::__construct("Invalid format: $actual, expects: $expects", $code, $previous);
        }
    }
}
