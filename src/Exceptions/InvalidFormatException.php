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
     * @param mixed $actual The actual format encountered.
     * @param string|string[] $expects An array of expected formats.
     * @param int $code The exception code.
     * @param Exception|null $previous The previous exception used for exception chaining.
     */
    public function __construct($actual, $expects = [], $code = 0, $previous = null)
    {
        $actual = json_encode($actual);
        if (empty($expects)) {
            parent::__construct("Invalid format: `$actual`", $code, $previous);
        } elseif (is_array($expects)) {
            foreach ($expects as $index => $expect) {
                $expects[$index] = '`' . trim($expect) . '`';
            }
            $expects = implode(', ', $expects);
            parent::__construct("Invalid format: `$actual`, expects: $expects", $code, $previous);
        } else {
            parent::__construct("Invalid format: `$actual`, expects: `$expects`", $code, $previous);
        }
    }
}
