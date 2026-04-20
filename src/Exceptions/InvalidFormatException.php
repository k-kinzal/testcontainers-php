<?php

namespace Testcontainers\Exceptions;

use Exception;

use function Testcontainers\ensure;

/**
 * Exception thrown when an invalid format is encountered.
 */
class InvalidFormatException extends Exception
{
    /**
     * InvalidFormatException constructor.
     *
     * @param mixed           $actual   the actual format encountered
     * @param string|string[] $expects  an array of expected formats
     * @param int             $code     the exception code
     * @param null|Exception $previous the previous exception used for exception chaining
     */
    public function __construct($actual, $expects = [], $code = 0, $previous = null)
    {
        ensure(is_string($expects) || is_array($expects), '$expects must be string|array');
        ensure(is_int($code), '$code must be int');
        ensure($previous === null || $previous instanceof Exception, '$previous must be null|Exception');

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
