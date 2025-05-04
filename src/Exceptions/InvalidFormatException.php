<?php

namespace Testcontainers\Exceptions;

use Exception;

/**
 * Exception thrown when an invalid format is encountered.
 */
class InvalidFormatException extends \Exception
{
    /**
     * InvalidFormatException constructor.
     *
     * @param mixed           $actual   the actual format encountered
     * @param string|string[] $expects  an array of expected formats
     * @param int             $code     the exception code
     * @param null|\Exception $previous the previous exception used for exception chaining
     */
    public function __construct($actual, $expects = [], $code = 0, $previous = null)
    {
        $actual = json_encode($actual);
        if (empty($expects)) {
            parent::__construct("Invalid format: `{$actual}`", $code, $previous);
        } elseif (is_array($expects)) {
            foreach ($expects as $index => $expect) {
                $expects[$index] = '`'.trim($expect).'`';
            }
            $expects = implode(', ', $expects);
            parent::__construct("Invalid format: `{$actual}`, expects: {$expects}", $code, $previous);
        } else {
            parent::__construct("Invalid format: `{$actual}`, expects: `{$expects}`", $code, $previous);
        }
    }
}
